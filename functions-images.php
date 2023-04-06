<?php

use Timmy\Timmy;
use Timmy\Helper;

/**
 * Frontend functions for Timmy.
 */

/**
 * Returns the src attr together with optional alt and title attributes for a TimberImage.
 *
 * @param int|Timber\Image $timber_image Instance of TimberImage or Attachment ID.
 * @param string|array     $size         The size which you want to access.
 *
 * @return string|bool Src and alt attributes. False if image can’t be found.
 */
function get_timber_image( $timber_image, $size ) {
	$image = Timmy::get_image( $timber_image, $size );

	if ( ! $image ) {
		return false;
	}

	$src = $image->src();

	if ( ! $src ) {
		return false;
	}

	return Helper::get_attribute_html( [
		'src' => $src,
		'alt' => $image->alt(),
	] );
}

/**
 * Returns the src (url) for a TimberImage.
 *
 * @param  int|Timber\Image $timber_image Instance of TimberImage or attachment ID.
 * @param  string|array     $size         Size key or array of the image to return.
 * @return string|bool Image src. False if image can’t be found.
 */
function get_timber_image_src( $timber_image, $size ) {
	$image = Timmy::get_image( $timber_image, $size );

	if ( ! $image ) {
		return false;
	}

	return $image->src();
}

/**
 * Returns the srcset for a TimberImage.
 *
 * @param int|Timber\Image $timber_image Instance of Timber\Image or attachment ID.
 * @param string|array     $size         Size key or array of the image to return.
 * @param array            $args         Optional args for the srcset.
 *
 * @return string|bool Image src. False if image can’t be found or no srcset is available.
 */
function get_timber_image_srcset( $timber_image, $size, $args = [] ) {
	$image = Timmy::get_image( $timber_image, $size );

	if ( ! $image ) {
		return false;
	}

	return $image->srcset( $args );
}

/**
 * Get the image attributes (alt and title) for an image.
 *
 * This will always include the alt tag. For accessibility, the alt tag needs to be included
 * even if it is empty.
 *
 * @since 0.14.0
 *
 * @param Timber\Image $timber_image Instance of TimberImage.
 *
 * @return array|false An array with alt and title attributes. False if image can’t be found.
 */
function get_timber_image_texts( $timber_image ) {
	$image = Timmy::get_image( $timber_image, [] );

	if ( ! $image ) {
		return false;
	}

	return [ 'alt' => $image->alt() ];
}

/**
 * Gets the image alt text.
 *
 * @since 1.0.0
 *
 * @param int|Timber\Image $timber_image Image ID or instance of TimberImage.
 *
 * @return false|string False on error or image alt text on success.
 */
function get_timber_image_alt( $timber_image ) {
	$image = Timmy::get_image( $timber_image, [] );

	if ( ! $image ) {
		return false;
	}

	return $image->alt();
}

/**
 * Gets the image caption.
 *
 * @since 1.0.0
 *
 * @param int|Timber\Image $timber_image Image ID or instance of TimberImage.
 *
 * @return false|string False on error or caption on success.
 */
function get_timber_image_caption( $timber_image ) {
	$image = Timmy::get_image( $timber_image, [] );

	if ( ! $image ) {
		return false;
	}

	return $image->caption();
}

/**
 * Gets the image description.
 *
 * @since 1.0.0
 *
 * @param int|Timber\Image $timber_image Image ID or instance of TimberImage.
 *
 * @return false|string False on error or image description on success.
 */
function get_timber_image_description( $timber_image ) {
	$image = Timmy::get_image( $timber_image, [] );

	if ( ! $image ) {
		return false;
	}

	return $image->description();
}

/**
 * Gets all image attributes for a responsive TimberImage.
 *
 * This function is useful if you want to change some of the attributes before outputting them.
 *
 * @since 0.14.0
 *
 * @param int|Timber\Image|\Timmy\Image $timber_image Instance of TimberImage.
 * @param string                        $size         Size key of the image to return the
 *                                                    attributes for.
 * @param array                         $args         Optional. Array of options. See
 *                                                    get_timber_image_responsive_src() for possible
 *                                                    options.
 *
 * @return bool|array An associative array of HTML attributes. False if image can’t be found.
 */
function get_timber_image_attributes_responsive( $timber_image, $size, $args = array() ) {
	$image = Timmy::get_image( $timber_image, $size );

	if ( ! $image ) {
		return false;
	}

	// Return attributes as array.
	$args = wp_parse_args( $args, [
		'return_format' => 'array',
	] );

	$attributes = [];

	$attributes['alt'] = $image->alt();

	return array_merge(
		get_timber_image_responsive_src( $timber_image, $size, $args ),
		$attributes
	);
}

/**
 * Gets the picture markup used for modern image formats using a fallback source.
 *
 * @since 1.0.0
 *
 * @param int|\Timber\Image $timber_image Instance of Timber\Image or Attachment ID.
 * @param string|array      $size         Timmy image size.
 * @param array             $args         Optional. Array of options. See
 *                                        get_timber_image_responsive_src() for possible
 *                                        options.
 *
 * @return false|string
 */
function get_timber_picture_responsive( $timber_image, $size, $args = [] ) {
	$image = Timmy::get_image( $timber_image, $size );

	if ( ! $image ) {
		return false;
	}

	return $image->picture_responsive( $args );
}

/**
 * Gets the fallback image for a picture image
 *
 * @since 1.0.0
 *
 * @param int|\Timber\Image $timber_image Instance of Timber\Image or Attachment ID.
 * @param string|array      $size         Timmy image size.
 *
 * @return false|string
 */
function get_timber_picture_fallback_image( $timber_image, $size ) {
	$image = Timmy::get_image( $timber_image, $size );

	if ( ! $image ) {
		return false;
	}

	return $image->picture_fallback_image();
}

/**
 * Get the responsive markup for a TimberImage.
 *
 * @param Timber\Image|int $timber_image Instance of TimberImage or Attachment ID.
 * @param string           $size         Size key of the image to return.
 * @param array            $args         Optional. Array of options. See
 *                                       get_timber_image_responsive_src() for a list of args
 *                                       that can be used.
 *
 * @return string|false Image srcset, sizes, width, height and alt attributes. False if image can’t
 *                      be found.
 */
function get_timber_image_responsive( $timber_image, $size, $args = array() ) {
	$image = Timmy::get_image( $timber_image, $size );

	if ( ! $image ) {
		return false;
	}

	return Helper::get_attribute_html( get_timber_image_attributes_responsive( $image, $size, $args ) );
}

/**
 * Get srcset and sizes for a TimberImage.
 *
 * This is practically the same as get_timber_image_responsive(), just without an alt tag.
 *
 * @todo Merge this again with get_timber_image_responsive() and add the option to control the
 *       alt attribute there.
 *
 * @param Timber\Image|int $timber_image Instance of TimberImage or Attachment ID.
 * @param string|array     $size         Size key or array of the image to return.
 * @param array            $args {
 *      Optional. Array of options.
 *
 *      @type bool   $attr_width    Whether to add a width attribute to an image, if needed.
 *                                  Default true.
 *      @type bool   $attr_height   Whether to add a height attribute to an image, if needed.
 *                                  Default true.
 *      @type bool   $lazy_srcset   Whether the srcset attribute should be prepended with
 *                                  "data-". Default false.
 *      @type bool   $lazy_src      Whether the src attribute should be prepended with "data-".
 *                                  Default false.
 *      @type string $return_format What format should be returned. Can either be 'string' or
 *                                  'array'. Default 'string'.
 * }
 * @return string|bool|array Image srcset and sizes attributes. False if image can’t be found.
 */
function get_timber_image_responsive_src( $timber_image, $size, $args = array() ) {
	$image = Timmy::get_image( $timber_image, $size );

	if ( ! $image ) {
		return false;
	}

	$args = wp_parse_args( $args, [
		'return_format' => 'string',
	] );

	$attributes = $image->responsive_attributes( $args );

	if ( 'array' === $args['return_format'] ) {
		return $attributes;
	}

	return Helper::get_attribute_html( $attributes );
}

/**
 * Get a responsive image based on an ACF field.
 *
 * @param string $name ACF Field Name.
 * @param string $size Size key of the image to return.
 *
 * @return string|bool Image srcset, sizes, alt and title attributes. False if image can’t be
 *                     found.
 */
function get_timber_image_responsive_acf( $name, $size ) {
	$image        = get_field( $name );
	$timber_image = Timmy::get_timber_image( $image );

	if ( ! $timber_image ) {
		return false;
	}

	return get_timber_image_responsive( $timber_image, $size );
}

/**
 * Prepares the srcset markup for lazy-loading.
 *
 * Updates attributes with a data-prefix. E.g. updates `srcset` with `data-srcset`.
 *
 * @since 0.13.3
 *
 * @param string $markup     Existing image HTML markup.
 * @param array  $attributes Optional. An array of attributes that should be replaced with
 *                           'data-' as a prefix. Default `[ 'srcset' ]`.
 *
 * @return string HTML markup.
 */
function make_timber_image_lazy( $markup, $attributes = [ 'srcset' ] ) {
	foreach ( $attributes as $attribute ) {
		$markup = str_replace(
			" {$attribute}=",
			" data-{$attribute}=",
			$markup
		);
	}

	return $markup;
}
