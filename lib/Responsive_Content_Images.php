<?php

namespace Timmy;

/**
 * Class Responsive_Content_Images
 *
 * @package Timmy
 */
class Responsive_Content_Images {
	/**
	 * Responsive_Content_Images constructor.
	 */
	public function __construct() {
		remove_filter( 'the_content', 'wp_make_content_images_responsive' );
		add_filter( 'the_content', array( $this, 'make_content_images_responsive' ) );
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

		$selected_images = $attachment_ids = array();

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
		 * To avoid making a database call for each image, a single query warms the object cache with
		 * the meta information for all images.
		 */
		if ( count( $attachment_ids ) > 1 ) {
			update_meta_cache( 'post', array_keys( $attachment_ids ) );
		}

		// Loop through images and apply responsive markup
		foreach ( $selected_images as $image => $attachment_id ) {
			$image_updated = $this->generate_srcset_and_sizes( $image, $attachment_id );
			$content = str_replace( $image, $image_updated, $content );
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
	public function generate_srcset_and_sizes( $image, $attachment_id  ) {
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

		// Get responsive image markup for srcset and sizes.
		$attr_responsive = get_timber_image_responsive( $attachment_id, $img_size, array(
			'attr_width' => true,
		) );

		// Bailout if markup couldn’t be generated.
		if ( ! $attr_responsive ) {
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

		// Add 'srcset' and 'sizes' attributes to the image markup.
		$image = preg_replace( '/<img ([^>]+?)[\/ ]*>/', '<img $1 ' . $attr_responsive . ' />', $image );

		return $image;
	}
}
