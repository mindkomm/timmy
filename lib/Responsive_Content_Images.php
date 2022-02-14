<?php

namespace Timmy;

/**
 * Class Responsive_Content_Images
 */
class Responsive_Content_Images {
	/**
	 * Args.
	 *
	 * @var array
	 */
	protected $args;

	final protected function __construct() {}

	/**
	 * Set args.
	 *
	 * @since 1.0.0
	 * @see Timmy::responsive_content_images()
	 *
	 * @param array $args Optional. An array of arguments for the Responsive Content Image handler.
	 *
	 * @return \Timmy\Responsive_Content_Images
	 */
	public static function instance( array $args = [] ) {
		$self = new self;

		$self->args = wp_parse_args( $args, [
			'map_sizes'       => [],
			'content_filters' => [
				'the_content' => 10,
			],
		] );

		return $self;
	}

	/**
	 * Inits hooks.
	 */
	public function init() {
		/**
		 * Remove the default filter used by WordPress.
		 *
		 * As of WordPress 5.5, the wp_make_content_images_responsive() function is deprecated and
		 * replaced with wp_filter_content_tags().
		 *
		 * @see wp_filter_content_tags()
		 */
		if ( function_exists( 'wp_filter_content_tags' ) ) {
			remove_filter( 'the_content', 'wp_filter_content_tags' );
		} else {
			remove_filter( 'the_content', 'wp_make_content_images_responsive' );
		}

		// Add content filters.
		foreach ( $this->args['content_filters'] as $filter => $priority ) {
			add_filter( $filter, [ $this, 'make_content_images_responsive' ], $priority );
		}

		// Remove width attribute from <figure> tag.
		add_filter( 'img_caption_shortcode_width', array( $this, 'fix_figure_width' ) );
	}

	/**
	 * Filter content and apply responsive image markup to images.
	 *
	 * @see wp_filter_content_tags()
	 *
	 * @param string $content Post content.
	 *
	 * @return string Filtered content
	 */
	public function make_content_images_responsive( $content ) {
		// Select images in content.
		if ( ! preg_match_all( '/<img [^>]+>/', $content, $classic_images ) ) {
			return $content;
		}

		/**
		 * Check for Gutenberg blocks.
		 *
		 * This is possibly not the best way to do it, but it works.
		 *
		 * Matches all figures with class "wp-block-image" and "size-" classes. There are two
		 * capturing groups:
		 * - The CSS classes.
		 * - The part after "size-" to catch the name of the image size that’s used.
		 */
		if ( preg_match_all(
			'/<figure class="([^"]*wp-block-image.*?size-([\w_\-\/.]+).*?)".*?><img [^>]+>.*<\/figure>/',
			$content,
			$block_images
		) ) {
			$content = $this->handle_block_images( $content, $block_images );
		}

		return $this->handle_classic_images( $content, $classic_images );
	}

	/**
	 * Handles images define as blocks.
	 *
	 * @since 0.14.5
	 *
	 * @param string $content      Post content.
	 * @param array  $block_images An array of regex matches.
	 *
	 * @return string
	 */
	public function handle_block_images( $content, $block_images ) {
		$selected_images = array();
		$attachment_ids  = array();

		/**
		 * Loop through possible images and get attachment IDs.
		 *
		 * Ignore images that already contain a srcset.
		 */
		foreach ( $block_images[0] as $match_key => $figure ) {
			// Get all images.
			if ( ! preg_match( '/<img [^>]+>/', $figure, $image_match ) ) {
				continue;
			}

			$classes    = explode( ' ', $block_images[1][ $match_key ] );
			$image_size = $block_images[2][ $match_key ];
			$image      = $image_match[0];

			// Bailout if image size couldn’t be read.
			if ( ! $image_size ) {
				continue;
			}

			if ( false === strpos( $image, ' srcset=' )
				&& preg_match( '/wp-image-([0-9]+)/i', $image, $class_id )
				&& ( $attachment_id = absint( $class_id[1] ) )
			) {
				/**
				 * If exactly the same image tag is used more than once, overwrite it.
				 * All identical tags will be replaced later with str_replace().
				 */
				$selected_images[ $image ] = [
					'attachment_id' => $attachment_id,
					'image_size'    => $image_size,
					'is_resized'    => in_array( 'is-resized', $classes, true ),
				];

				// Overwrite the ID when the same image is included more than once.
				$attachment_ids[ $attachment_id ] = true;
			}
		}

		return $this->handle_images( $content, $selected_images, $attachment_ids );
	}

	/**
	 * Handles images added through the classic editor.
	 *
	 * @param string $content        Post content.
	 * @param array  $classic_images An array of regex matches.
	 *
	 * @return string
	 */
	public function handle_classic_images( $content, $classic_images ) {
		$selected_images = array();
		$attachment_ids  = array();

		/**
		 * Loop through possible images and get attachment ids.
		 *
		 * Ignore images that already contain a srcset.
		 */
		foreach ( $classic_images[0] as $image ) {
			if ( false === strpos( $image, ' srcset=' )
				&& preg_match( '/wp-image-([0-9]+)/i', $image, $class_id )
				&& ( $attachment_id = absint( $class_id[1] ) )
			) {
				// Select image size from classname starting with 'size-'.
				$image_size = preg_match( '/ size-([^\s"]+)/', $image, $match_size )
					? $match_size[1]
					: false;

				// Bailout if image size couldn’t be read.
				if ( ! $image_size ) {
					continue;
				}

				/**
				 * If exactly the same image tag is used more than once, overwrite it.
				 * All identical tags will be replaced later with str_replace().
				 */
				$selected_images[ $image ] = [
					'attachment_id' => $attachment_id,
					'image_size'    => $image_size,
				];

				// Overwrite the ID when the same image is included more than once.
				$attachment_ids[ $attachment_id ] = true;
			}
		}

		return $this->handle_images( $content, $selected_images, $attachment_ids );
	}

	/**
	 * Handles replacing the image markup with Timmy markup.
	 *
	 * @param string $content         Post content.
	 * @param array  $selected_images An array of image data.
	 * @param array  $attachment_ids  An array of attachment IDs to warm object cache.
	 *
	 * @return string|string[]
	 */
	public function handle_images( $content, $selected_images, $attachment_ids ) {
		/**
		 * Warm object cache for use with 'get_post_meta()'.
		 *
		 * To avoid making a database call for each image, a single query warms the object cache
		 * with the meta information for all images.
		 */
		if ( count( $attachment_ids ) > 1 ) {
			update_meta_cache( 'post', array_keys( $attachment_ids ) );
		}

		// Loop through images and apply responsive markup.
		foreach ( $selected_images as $image => $image_data ) {
			$image_updated = $this->generate_srcset_and_sizes( $image, $image_data );
			$content       = str_replace( $image, $image_updated, $content );
		}

		return $content;
	}

	/**
	 * Updates image tag with responsive srcset and sizes attributes.
	 *
	 * @param string $image HTML image tag.
	 * @param array  $data  Image data.
	 *
	 * @return string Reponsive image markup
	 */
	public function generate_srcset_and_sizes( $image, $data ) {
		// Ensure the image meta exists.
		$image_src = preg_match( '/src="([^"]+)"/', $image, $match_src )
			? $match_src[1]
			: '';

		list( $image_src ) = explode( '?', $image_src );

		// Return early if we couldn't get the image source.
		if ( ! $image_src ) {
			return $image;
		}

		$img_size      = $data['image_size'];
		$attachment_id = $data['attachment_id'];
		$is_resized    = isset( $data['is_resized'] ) ? $data['is_resized'] : false;

		// Maybe select replacement size.
		if ( ! empty( $this->args['map_sizes'] ) ) {
			if ( is_array( $this->args['map_sizes'] )
				&& in_array( $img_size, array_keys( $this->args['map_sizes'] ), true )
			) {
				$img_size = $this->args['map_sizes'][ $img_size ];
			} else {
				$img_size = $this->args['map_sizes'];
			}
		}

		// Get responsive image markup for srcset and sizes.
		$attributes = get_timber_image_attributes_responsive(
			$attachment_id,
			$img_size
		);

		// Bailout if markup couldn’t be generated.
		if ( ! $attributes ) {
			return $image;
		}

		// Remove existing src.
		$image = preg_replace( '/ src="([^"]+)"/', '', $image );

		/**
		 * Remove attributes that are handled by get_timber_image_responsive_src().
		 *
		 * - Keep width and height attributes if image was resized in the block editor.
		 * - Always remove the title attribute.
		 * - Remove the alt attribute if it is empty.
		 *
		 * @see get_timber_image_responsive_src()
		 */
		if ( ! $is_resized ) {
			$image = preg_replace( '/ height="([^"]+)"/', '', $image );
			$image = preg_replace( '/ width="([^"]+)"/', '', $image );
		} else {
			// Remove attributes from Timmy markup that we should ignore.
			foreach ( [ 'width', 'height', 'styles' ] as $attribute ) {
				if ( array_key_exists( $attribute, $attributes ) ) {
					unset( $attributes[ $attribute ] );
				}
			}
		}

		// Remove title attribute.
		$image = preg_replace( '/ title="([^"]*)"/', '', $image );

		$non_empty_alt = preg_match( '/ alt="([^"]*)"/', $image, $alt_match );

		/**
		 * Handle alt attribute.
		 *
		 * If a non-empty alt attribute is found for an image, use that one and leave it there,
		 * otherwise fall back to the default Timmy alt text.
		 */
		if ( $non_empty_alt && ! empty( $alt_match[1] ) ) {
			unset( $attributes['alt'] );
		} else {
			$image = preg_replace( '/ alt="([^"]*)"/', '', $image );
		}

		// Replace closing tag.
		$image = preg_replace( '/\s?\/>/', '>', $image );

		// Remove class attribute and save class attribute content in attributes array.
		if ( preg_match( '/ class="([^"]+)"/', $image, $class_matches ) ) {
			$attributes['class'] = $class_matches[1];
			$image               = preg_replace( '/ class="([^"]+)"/', '', $image );
		}

		/**
		 * Filters image attributes used for a responsive content image.
		 *
		 * @param array  $attributes    A key-value array of HTML attributes.
		 * @param int    $attachment_id The attachment ID of the image.
		 * @param string $img_size      The image size key.
		 */
		$attributes = apply_filters(
			'timmy/responsive_content_image/attributes',
			$attributes,
			$attachment_id,
			$img_size
		);

		// Replace image markup.
		$image = preg_replace_callback(
			'/<img([^>]*)>/',
			function( $matches ) use ( $attributes ) {
				$existing_attributes = trim( $matches[1] );

				if ( ! empty( $existing_attributes ) ) {
					$existing_attributes = ' ' . $existing_attributes;
				}

				return sprintf(
					'<img%1$s %2$s>',
					$existing_attributes,
					trim( Helper::get_attribute_html( $attributes ) )
				);
			},
			$image
		);

		/**
		 * Filters the image HTML markup.
		 *
		 * This filter can be used to append content to an image.
		 *
		 * @param string $image         The image HTML markup.
		 * @param int    $attachment_id The attachment ID of the image.
		 * @param string $img_size      The image size key.
		 */
		$image = apply_filters(
			'timmy/responsive_content_image',
			$image,
			$attachment_id,
			$img_size
		);

		return $image;
	}

	/**
	 * Removes width attribute from figure tags for images added in editor.
	 *
	 * WordPress automatically adds the width of the image to a surrounding figure tag. By returning
	 * `0`, we can disable this feature.
	 *
	 * @since 0.14.0
	 * @param int $width Width of the caption in pixels.
	 *
	 * @return int
	 */
	public function fix_figure_width( $width ) {
		return 0;
	}
}
