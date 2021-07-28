<?php

namespace Timmy;

/**
 * Class Helper
 *
 * @package Timmy
 */
class Helper {
	/**
	 * Image configuration cache.
	 *
	 * @var array|null Image configuration array.
	 */
	public static $sizes = null;

	/**
	 * Get image size configuration.
	 *
	 * Try first to get image sizes from cache. If the cache is not set, try to get image sizes from the timmy/sizes
	 * filter. And as a last resort, try to get images from the discouraged get_image_sizes() function.
	 *
	 * @since 0.13.0
	 */
	public static function get_image_sizes() {
		/**
		 * Filters whether the internal sizes cache should be skipped.
		 *
		 * Mainly used for testing.
		 *
		 * @since 0.14.8
		 */
		$use_sizes_cache = apply_filters( 'timmy/sizes/use_cache', true );

		// Bailout early if cached image configuration is available.
		if ( self::$sizes && $use_sizes_cache ) {
			return self::$sizes;
		}

		/**
		 * Filters image sizes used in Timmy.
		 *
		 * @since 0.13.0
		 *
		 * @param array $sizes Image configuration array. Default array().
		 */
		$sizes = apply_filters( 'timmy/sizes', array() );

		/**
		 * Fallback for get_image_sizes() function
		 *
		 * TODO: deprecate in 1.0.0
		 */
		if ( empty( $sizes ) && function_exists( 'get_image_sizes' ) ) {
			$sizes = get_image_sizes();
		}

		// Cache sizes for next requests.
		self::$sizes = $sizes;

		return $sizes;
	}

	/**
	 * Get an image size from image config.
	 *
	 * @since 0.11.0
	 *
	 * @param array|string $size Image size configuration array or image size key.
	 *
	 * @return array|bool Image size configuration array.
	 */
	public static function get_image_size( $size ) {
		// Check for a directly passed image configuration array.
		if ( is_array( $size ) ) {
			return $size;
		}

		$sizes = self::get_image_sizes();

		// Return found image size.
		if ( isset( $sizes[ $size ] ) ) {
			return $sizes[ $size ];
		}

		self::notice( "Image size \"{$size}\" does not exist in your image configuration." );

		return false;
	}

	/**
	 * Get thumbnail size from image config.
	 *
	 * When an image is requested without a size name or with dimensions only, try to return the thumbnail.
	 * Otherwise take the first image in the image array.
	 *
	 * @return array Image size configuration array.
	 */
	public static function get_thumbnail_size() {
		$sizes = self::get_image_sizes();

		if ( isset( $sizes['thumbnail'] ) ) {
			return $sizes['thumbnail'];
		}

		$img_size = reset( $sizes );

		return $img_size;
	}

	/**
	 * Get width and height for an image size.
	 *
	 * @param array $img_size Image size configuration array.
	 * @return array Width and height.
	 */
	public static function get_dimensions_for_size( $img_size ) {
		$width  = absint( $img_size['resize'][0] );
		$height = isset( $img_size['resize'][1] ) ? absint( $img_size['resize'][1] ) : 0;

		return array( $width, $height );
	}

	/**
	 * Get width and height for a srcset size.
	 *
	 * @param array     $resize      Resize configuration array.
	 * @param array|int $srcset_size Srcset definition.
	 *
	 * @return array Width and height.
	 */
	public static function get_dimensions_for_srcset_size( $resize, $srcset_size ) {
		// Get width and height for the additional src
		if ( is_array( $srcset_size ) ) {
			$width  = $srcset_size[0];
			$height = isset( $srcset_size[1] ) ? $srcset_size[1] : 0;
		} else {
			// Check for x-notation, e.g. '2x' or '1.5x'.
			if ( ! is_numeric( $srcset_size ) && 'x' === substr( $srcset_size, -1, 1 ) ) {
				$srcset_size = (float) rtrim( $srcset_size, 'x' );
			}

			$width  = (int) round( $resize[0] * $srcset_size );
			$height = isset( $resize[1] ) ? (int) round( $resize[1] * $srcset_size ) : 0;
		}

		return array( $width, $height );
	}

	/**
	 * Get crop value from a resize parameter.
	 *
	 * @param array $img_size Image size configuration array.
	 *
	 * @return string Crop value.
	 */
	public static function get_crop_for_size( $img_size ) {
		return isset( $img_size['resize'][2] ) ? $img_size['resize'][2] : 'default';
	}

	/**
	 * Get force value from a resize parameter.
	 *
	 * @param array $img_size Image size configuration array.
	 *
	 * @return bool Force value.
	 */
	public static function get_force_for_size( $img_size ) {
		return isset( $img_size['resize'][3] ) ? $img_size['resize'][3] : false;
	}

	/**
	 * Returns the HTML for an array of HTML tag attributes.
	 *
	 * @since 0.14.0
	 *
	 * @param array $attributes An associative array of HTML attributes.
	 *
	 * @return string HTML attribute string to be used in an HTML tag.
	 */
	public static function get_attribute_html( $attributes = array() ) {
		$html = '';

		if ( ! $attributes ) {
			return $html;
		}

		foreach ( $attributes as $key => $attribute ) {
			$html .= ' ' . esc_attr( $key ) . '="' . esc_attr( $attribute ) . '"';
		}

		return $html;
	}

	/**
	 * Gets original attachment URL.
	 *
	 * In WordPress 5.3, WordPress added new functionality to create scaled images. This can be
	 * disabled through the `big_image_size_threshold` filter. However, when getting the attachment
	 * URL, we should always get the original size to generate sizes, otherwise we lose a lot of
	 * quality.
	 *
	 * @since 0.14.4
	 *
	 * @param int $attachment_id An attachment ID.
	 *
	 * @return false|string
	 */
	public static function get_original_attachment_url( $attachment_id ) {
		/**
		 * The wp_get_original_image_url() function checks for wp_attachment_is_image(). SVG images
		 * don’t qualify as images, so to not return false here, we need to check for
		 * wp_attachment_is_image() before running wp_get_original_image_url().
		 */
		if ( function_exists( 'wp_get_original_image_url' )
			&& wp_attachment_is_image( $attachment_id )
		) {
			return wp_get_original_image_url( $attachment_id );
		}

		return wp_get_attachment_url( $attachment_id );
	}

	/**
	 * Gets cached mime types.
	 *
	 * Useful in combination with wp_check_filetype(), where you should pass a second parameter to
	 * prevent get_allowed_mime_types() from being called too many times.
	 *
	 * @since 0.14.6
	 * @see \wp_check_filetype()
	 *
	 * @return string[]|null
	 */
	public static function get_mime_types() {
		static $mime_types = null;

		if ( ! isset( $mime_types ) ) {
			$mime_types = wp_get_mime_types();
		}

		return $mime_types;
	}

	/**
	 * Output an error message.
	 *
	 * Triggers a notice, but only in development environments, when WP_DEBUG is set to true.
	 *
	 * @since 0.11.0
	 *
	 * @param string $message The message to output.
	 */
	public static function notice( $message ) {
		if ( WP_DEBUG ) {
			trigger_error( $message, E_USER_NOTICE );
		}
	}
}
