<?php

namespace Timmy;

/**
 * Class Helper
 *
 * @package Timmy
 */
class Helper {
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

		$sizes = get_image_sizes();

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
		$sizes = get_image_sizes();

		if ( isset( $sizes['thumbnail'] ) ) {
			return $sizes['thumbnail'];
		}

		return $img_size = reset( $sizes );
	}

	/**
	 * Get width and height for an image size.
	 *
	 * @param array $img_size Image size configuration array.
	 * @return array Width and height.
	 */
	public static function get_dimensions_for_size( $img_size ) {
		$width = absint( $img_size['resize'][0] );
		$height = isset( $img_size['resize'][1] ) ? absint( $img_size['resize'][1] ) : 0;

		return array( $width, $height );
	}

	/**
	 * Get width and height for a srcset size.
	 *
	 * @param array $resize Resize configuration array.
	 * @param array|int $srcset_size Srcset definition.
	 * @return array Width and height.
	 */
	public static function get_dimensions_for_srcset_size( $resize, $srcset_size ) {
		// Get width and height for the additional src
		if ( is_array( $srcset_size ) ) {
			$width  = $srcset_size[0];
			$height = isset( $srcset_size[1] ) ? $srcset_size[1] : 0;
		} else {
			$width  = (int) round( $resize[0] * $srcset_size );
			$height = isset( $resize[1] ) ? (int) round( $resize[1] * $srcset_size ) : 0;
		}

		return array( $width, $height );
	}

	/**
	 * Get crop value from a resize parameter.
	 *
	 * @param array $img_size Image size configuration array.
	 * @return string Crop value.
	 */
	public static function get_crop_for_size( $img_size ) {
		return isset( $img_size['resize'][2] ) ? $img_size['resize'][2] : 'default';
	}

	/**
	 * Get force value from a resize parameter.
	 *
	 * @param array $img_size Image size configuration array.
	 * @return bool Force value.
	 */
	public static function get_force_for_size( $img_size ) {
		return isset( $img_size['resize'][3] ) ? $img_size['resize'][3] : false;
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
