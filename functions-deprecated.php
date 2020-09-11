<?php

use Timmy\Timmy;
use Timmy\Helper;

if ( ! function_exists( 'get_image_attr_html' ) ) :
	/**
	 * Returns the HTML for given alt and title attribute strings.
	 *
	 * @deprecated 0.14.0
	 * @todo Remove in v1.x
	 *
	 * @param  string $alt   Alt text.
	 * @param  string $title Title text.
	 * @return string HTML string for alt and title attributes.
	 */
	function get_image_attr_html( $alt, $title ) {
		Helper::notice( 'This function is deprecated and will be removed in v1.0 of Timmy' );

		return Helper::get_attribute_html( [
			'alt'   => $alt,
			'title' => $title,
		] );
	}
endif;

if ( ! function_exists( 'get_timber_image_attr' ) ) :
	/**
	 * Get the image attributes (alt and title) for a TimberImage.
	 *
	 * @deprecated 0.14.0
	 * @todo Remove in 1.x
	 *
	 * @param  Timber\Image $timber_image Instance of TimberImage.
	 * @return string|false HTML string for alt and title attributes. False if image can’t be found.
	 */
	function get_timber_image_attr( $timber_image ) {
		Helper::notice( 'This function is deprecated and will be removed in v1.0 of Timmy' );

		$timber_image = Timmy::get_timber_image( $timber_image );

		if ( ! $timber_image ) {
			return false;
		}

		return Helper::get_attribute_html( get_timber_image_texts( $timber_image ) );
	}
endif;

if ( ! function_exists( 'get_acf_image_attr' ) ) :
	/**
	 * Get image attributes for an image accessed via ACF.
	 *
	 * @deprecated 0.14.5
	 * @todo Remove in 1.x
	 *
	 * @param  array $image ACF Image.
	 * @return string Alt and title attribute.
	 */
	function get_acf_image_attr( $image ) {
		Helper::notice( 'This function is deprecated and will be removed in v1.0 of Timmy' );

		return Helper::get_attribute_html( [
			'alt'   => ! empty( $image['alt'] ) ? $image['alt'] : '',
			'title' => $image['description'],
		] );
	}
endif;
