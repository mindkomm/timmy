<?php

namespace Timmy;

/**
 * Class Responsive_Content_Images
 */
class Responsive_Content_Images {
	/**
	 * Responsive_Content_Images constructor.
	 *
	 * @param array $args {
	 *     Optional. An array of arguments for the Responsive Content Image handler.
	 *
	 *     @type array|string $map_sizes An associative array of size keys used in the content and
	 *                                   Timmy sizes to replace them with. E.g., when a large image
	 *                                   is used in the content, you could use `'large' => 'content`
	 *                                   to use the 'content' size from your image configuration
	 *                                   instead of the large size. If you use a string, all sizes
	 *                                   will be mapped to the size you pass in the string.
	 * }
	 */
	public function __construct( $args = array() ) {
		$this->args = wp_parse_args( $args, array(
			'map_sizes' => array(),
		) );

		// Remove the default filter used by WordPress.
		remove_filter( 'the_content', 'wp_make_content_images_responsive' );

		// Add our own custom filter.
		add_filter( 'the_content', array( $this, 'make_content_images_responsive' ) );

		// Remove width attribute from <figure> tag.
		add_filter( 'img_caption_shortcode_width', array( $this, 'fix_figure_width' ) );
	}

	/**
	 * Filter content and apply responsive image markup to images.
	 *
	 * @see wp_make_content_images_responsive()
	 *
	 * @param string $content Post content.
	 *
	 * @return string Filtered content
	 */
	public function make_content_images_responsive( $content ) {
		// Select images in content
		if ( ! preg_match_all( '/<img [^>]+>/', $content, $matches ) ) {
			return $content;
		}

		$selected_images = array();
		$attachment_ids  = array();

		/**
		 * Loop through possible images and get attachment ids.
		 *
		 * Ignore images that already contain a srcset.
		 */
		foreach ( $matches[0] as $image ) {
			if ( false === strpos( $image, ' srcset=' )
				&& preg_match( '/wp-image-([0-9]+)/i', $image, $class_id )
				&& ( $attachment_id = absint( $class_id[1] ) )
			) {
				/**
				 * If exactly the same image tag is used more than once, overwrite it.
				 * All identical tags will be replaced later with str_replace().
				 */
				$selected_images[ $image ] = $attachment_id;

				// Overwrite the ID when the same image is included more than once.
				$attachment_ids[ $attachment_id ] = true;
			}
		}

		/**
		 * Warm object cache for use with 'get_post_meta()'.
		 *
		 * To avoid making a database call for each image, a single query warms the object cache
		 * with the meta information for all images.
		 */
		if ( count( $attachment_ids ) > 1 ) {
			update_meta_cache( 'post', array_keys( $attachment_ids ) );
		}

		// Loop through images and apply responsive markup
		foreach ( $selected_images as $image => $attachment_id ) {
			$image_updated = $this->generate_srcset_and_sizes( $image, $attachment_id );
			$content       = str_replace( $image, $image_updated, $content );
		}

		return $content;
	}

	/**
	 * Updates image tag with responsive srcset and sizes attributes.
	 *
	 * @param string $image         HTML image tag.
	 * @param int    $attachment_id Attachment ID.
	 *
	 * @return string Reponsive image markup
	 */
	public function generate_srcset_and_sizes( $image, $attachment_id ) {
		// Ensure the image meta exists.
		$image_src = preg_match( '/src="([^"]+)"/', $image, $match_src ) ? $match_src[1] : '';

		list( $image_src ) = explode( '?', $image_src );

		// Return early if we couldn't get the image source.
		if ( ! $image_src ) {
			return $image;
		}

		// Select image size from classname starting with 'size-'.
		$img_size = preg_match( '/ size-([^\s"]+)/', $image, $match_size ) ? $match_size[1] : false;

		// Bailout if image size couldn’t be read.
		if ( ! $img_size ) {
			return $image;
		}

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

		// Remove existing src
		$image = preg_replace( '/ src="([^"]+)"/', '', $image );

		/**
		 * Remove width, height and alt attributes, because they are handled by
		 * get_timber_image_responsive_src().
		 *
		 * @see get_timber_image_responsive_src()
		 */
		$image = preg_replace( '/ height="([^"]+)"/', '', $image );
		$image = preg_replace( '/ width="([^"]+)"/', '', $image );
		$image = preg_replace( '/ alt="([^"]+)"/', '', $image );
		$image = preg_replace( '/ title="([^"]+)"/', '', $image );

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

		// Replace image markup
		$image = preg_replace(
			'/<img ([^>]+?)[\/ ]*>/',
			'<img $1 ' . Helper::get_attribute_html( $attributes ) . ' />',
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
