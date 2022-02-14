<?php

use Timmy\Timmy;
use Timmy\Helper;

/**
 * Frontend functions for Timmy.
 *
 * These functions are all pluggable, which means you can overwrite them if you add them to the
 * functions.php file of your theme.
 */

if ( ! function_exists( 'get_timber_image' ) ) :
	/**
	 * Returns the src attr together with optional alt and title attributes for a TimberImage.
	 *
	 * @param  int|Timber\Image $timber_image Instance of TimberImage or Attachment ID.
	 * @param  string|array     $size         The size which you want to access.
	 * @return string|bool Src, alt and title attributes. False if image can’t be found.
	 */
	function get_timber_image( $timber_image, $size ) {
		$timber_image = Timmy::get_timber_image( $timber_image );

		if ( ! $timber_image ) {
			return false;
		}

		$image = new \Timmy\Image( $timber_image, $size );

		$src = $image->src();

		if ( ! $src ) {
			return false;
		}

		return Helper::get_attribute_html( [
			'src' => $src,
			'alt' => $image->alt(),
		] );
	}
endif;

if ( ! function_exists( 'get_timber_image_src' ) ) :
	/**
	 * Returns the src (url) for a TimberImage.
	 *
	 * @param  int|Timber\Image $timber_image Instance of TimberImage or attachment ID.
	 * @param  string|array     $size         Size key or array of the image to return.
	 * @return string|bool Image src. False if image can’t be found.
	 */
	function get_timber_image_src( $timber_image, $size ) {
		$timber_image = Timmy::get_timber_image( $timber_image );

		if ( ! $timber_image ) {
			return false;
		}

		$image = new \Timmy\Image( $timber_image, $size );

		return $image->src();
	}
endif;

if ( ! function_exists( 'get_timber_image_srcset' ) ) :
	/**
	 * Returns the srcset for a TimberImage.
	 *
	 * @param  int|Timber\Image $timber_image Instance of Timber\Image or attachment ID.
	 * @param  string|array     $size         Size key or array of the image to return.
	 * @return string|bool Image src. False if image can’t be found or no srcset is available.
	 */
	function get_timber_image_srcset( $timber_image, $size ) {
		$timber_image = Timmy::get_timber_image( $timber_image );

		if ( ! $timber_image ) {
			return false;
		}

		$image = new \Timmy\Image( $timber_image, $size );

		return $image->srcset();
	}
endif;

if ( ! function_exists( 'get_timber_image_texts' ) ) :
	/**
	 * Get the image attributes (alt and title) for a TimberImage.
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
		$timber_image = Timmy::get_timber_image( $timber_image );

		if ( ! $timber_image ) {
			return false;
		}

		return [ 'alt' => $timber_image->alt() ];
	}
endif;

/**
 * Gets the image alt text.
 *
 * @since 0.15.0
 *
 * @param int|Timber\Image $timber_image Image ID or instance of TimberImage.
 *
 * @return false|string False on error or image alt text on success.
 */
function get_timber_image_alt( $timber_image ) {
	$timber_image = Timmy::get_timber_image( $timber_image );

	if ( ! $timber_image ) {
		return false;
	}

	return $timber_image->alt();
}

/**
 * Gets the image caption.
 *
 * @since 0.15.0
 *
 * @param int|Timber\Image $timber_image Image ID or instance of TimberImage.
 *
 * @return false|string False on error or caption on success.
 */
function get_timber_image_caption( $timber_image ) {
	$timber_image = Timmy::get_timber_image( $timber_image );

	if ( ! $timber_image ) {
		return false;
	}

	return $timber_image->caption;
}

/**
 * Gets the image description.
 *
 * @since 0.15.0
 *
 * @param int|Timber\Image $timber_image Image ID or instance of TimberImage.
 *
 * @return false|string False on error or image description on success.
 */
function get_timber_image_description( $timber_image ) {
	$timber_image = Timmy::get_timber_image( $timber_image );

	if ( ! $timber_image ) {
		return false;
	}

	return $timber_image->post_content;
}

if ( ! function_exists( 'get_timber_image_attributes_responsive' ) ) :
	/**
	 * Gets all image attributes for a responsive TimberImage.
	 *
	 * This function is useful if you want to change some of the attributes before outputting them.
	 *
	 * @since 0.14.0
	 *
	 * @param int|Timber\Image $timber_image Instance of TimberImage.
	 * @param string           $size         Size key of the image to return the attributes for.
	 * @param array            $args         Optional. Array of options. See
	 *                                       get_timber_image_responsive() for possible options.
	 *
	 * @return bool|array An associative array of HTML attributes. False if image can’t be found.
	 */
	function get_timber_image_attributes_responsive( $timber_image, $size, $args = array() ) {
		$timber_image = Timmy::get_timber_image( $timber_image );

		if ( ! $timber_image ) {
			return false;
		}

		$image = new \Timmy\Image( $timber_image, $size );

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
endif;

/**
 * Gets the picture markup used for modern image formats using a fallback source.
 * @since 0.15.0
 *
 * @param int|\Timber\Image $timber_image Instance of Timber\Image or Attachment ID.
 * @param string|array      $size         Timmy image size.
 *
 * @return false|string
 */
function get_timber_picture_responsive( $timber_image, $size, $args = [] ) {
	$timber_image = Timmy::get_timber_image( $timber_image );

	if ( ! $timber_image ) {
		return false;
	}

	$image = new \Timmy\Image( $timber_image, $size );

	$towebp = ! empty( $size['towebp'] );

	$mime_type = false;

	if ( $towebp ) {
		$mime_type = isset( $size['tojpg'] ) && $size['tojpg']
			? 'image/jpeg'
			: $image->mime_type();
	}

	$attributes = [
		'type'   => $mime_type,
		'sizes'  => $attributes['sizes'] ?? [],
		'srcset' => get_timber_image_srcset( $timber_image, array_merge( $size, [
			'is_webp_fallback' => $towebp,
		] ) ),
	];

	$html = '<source' . Helper::get_attribute_html( $attributes ) . '>' . PHP_EOL;

	if ( $towebp ) {
		$source_attributes = [
			'type'   => 'image/webp',
			'sizes'  => $attributes['sizes'] ?? [],
			'srcset' => get_timber_image_srcset( $timber_image, $size ),
			'loading' => $image->loading(),
		];

		$html .= '<source' . Helper::get_attribute_html( $source_attributes ) . '>' . PHP_EOL;
	}

	// Add fallback.
	$html .= get_timber_picture_fallback_image( $timber_image, $size );

	return $html;
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
	$timber_image = Timmy::get_timber_image( $timber_image );

	if ( ! $timber_image ) {
		return false;
	}

	$size = Helper::get_image_size( $size );

	$fallback_attributes = [
		'src'     => get_timber_image_src( $timber_image, array_merge( $size, [
			'towebp' => false,
		] ) ),
		'alt'     => get_timber_image_alt( $timber_image ) ?: '',
		'loading' => $attributes['loading'] ?? false,
	];

	return '<img' . Helper::get_attribute_html( $fallback_attributes ) . '>';
}

if ( ! function_exists( 'get_timber_image_responsive' ) ) :
	/**
	 * Get the responsive markup for a TimberImage.
	 *
	 * @param Timber\Image|int $timber_image Instance of TimberImage or Attachment ID.
	 * @param string           $size         Size key of the image to return.
	 * @param array            $args         Optional. Array of options. See
	 *                                       get_timber_image_responsive_src() for a list of args
	 *                                       that can be used.
	 *
	 * @return string|bool Image srcset, sizes, alt and title attributes. False if image can’t be
	 *                     found.
	 */
	function get_timber_image_responsive( $timber_image, $size, $args = array() ) {
		return Helper::get_attribute_html( get_timber_image_attributes_responsive( $timber_image, $size, $args ) );
	}
endif;

if ( ! function_exists( 'get_timber_image_responsive_src' ) ) :
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
	 *                                  Default false.
	 *      @type bool   $attr_height   Whether to add a height attribute to an image, if needed.
	 *                                  Default false.
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
		$timber_image = Timmy::get_timber_image( $timber_image );

		if ( ! $timber_image ) {
			return false;
		}

		/**
		 * Default arguments for image markup.
		 *
		 * @since 0.12.0
		 */
		$default_args = array(
			'attr_width'    => true,
			'attr_height'   => true,
			'lazy_srcset'   => false,
			'lazy_src'      => false,
			'lazy_sizes'    => false,
			'loading'       => 'lazy',
			'return_format' => 'string',
		);

		$args     = wp_parse_args( $args, $default_args );
		$img_size = Helper::get_image_size( $size );

		if ( ! $img_size ) {
			return false;
		}

		$image = new \Timmy\Image( $timber_image, $img_size );

		$image->set_args( $args );

		$attributes = $image->responsive_attributes();

		if ( 'array' === $args['return_format'] ) {
			return $attributes;
		}

		return Helper::get_attribute_html( $attributes );
	}
endif;

if ( ! function_exists( 'get_timber_image_responsive_acf' ) ) :
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
endif;

/**
 * Gets the image width for a size.
 *
 * @since 0.15.0
 *
 * @param int|Timber\Image $timber_image Image ID or instance of TimberImage.
 * @param string|array     $size         Size key or size configuration array.
 *
 * @return false|int False on error or image width.
 */
function get_timber_image_width( $timber_image, $size ) {
	$timber_image = Timmy::get_timber_image( $timber_image );

	if ( ! $timber_image ) {
		return false;
	}

	/**
	 * Get meta data not filtered by Timmy.
	 *
	 * @todo: Add a PR to Timber repository that saves the width and the height of an image in the
	 *      metadata. Timber already calls wp_get_attachment_metadata(), but discards the width and
	 *      height.
	 */
	$meta_data = wp_get_attachment_metadata( $timber_image->ID, true );

	$max_width  = $meta_data['width'];
	$max_height = $meta_data['height'];

	$size = Helper::get_image_size( $size );

	list( $width, $height ) = Helper::get_dimensions_for_size( $size );
	list( $width ) = Helper::get_dimensions_upscale( $width, $height, [
		'upscale'    => Helper::get_upscale_for_size( $size ),
		'resize'     => $size['resize'],
		'max_width'  => $max_width,
		'max_height' => $max_height,
	] );

	return $width;
}

/**
 * Gets the image height for a size.
 *
 * @since 0.15.0
 *
 * @param int|Timber\Image $timber_image Image ID or instance of TimberImage.
 * @param string|array     $size         Size key.
 *
 * @return false|int False on error or image height.
 */
function get_timber_image_height( $timber_image, $size ) {
	$timber_image = Timmy::get_timber_image( $timber_image );

	if ( ! $timber_image ) {
		return false;
	}

	// Get meta data not filtered by Timmy.
	$meta_data  = wp_get_attachment_metadata( $timber_image->ID, true );
	$max_width  = $meta_data['width'];
	$max_height = $meta_data['height'];

	$size = Helper::get_image_size( $size );

	list( $width, $height ) = Helper::get_dimensions_for_size( $size );

	$height = Helper::maybe_fix_height( $height, $width, $max_width, $max_height );

	list( , $height ) = Helper::get_dimensions_upscale( $width, $height, [
		'upscale'    => Helper::get_upscale_for_size( $size ),
		'resize'     => $size['resize'],
		'max_width'  => $max_width,
		'max_height' => $max_height,
	] );

	return $height;
}

if ( ! function_exists( 'get_post_thumbnail' ) ) :
	/**
	 * Get Post Thumbnail source together with alt and title attributes.
	 *
	 * @param int    $post_id The post id to get the thumbnail from.
	 * @param string $size    Size key of the image to return.
	 *
	 * @return string|bool Image src together with alt and title attributes. False if no image can’t
	 *                     be found.
	 */
	function get_post_thumbnail( $post_id, $size = 'post-thumbnail' ) {
		$thumbnail_src = get_post_thumbnail_src( $post_id, $size );

		if ( ! $thumbnail_src ) {
			return false;
		}

		$thumb_id   = get_post_thumbnail_id( $post_id );
		$attachment = get_post( $thumb_id );

		// Alt attributes are saved as post meta
		$alt = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );

		// We take the image description for the title
		$title = $attachment->post_content;

		return Helper::get_attribute_html( [
			'src'   => $thumbnail_src,
			'alt'   => $alt,
			'title' => $title,
		] );
	}
endif;

if ( ! function_exists( 'get_post_thumbnail_src' ) ) :
	/**
	 * Get Post Thumbnail image source at given size.
	 *
	 * @param int    $post_id The post id to get the thumbnail from.
	 * @param string $size    Size key of the image to return.
	 *
	 * @return string|bool Image src. False if not an image.
	 */
	function get_post_thumbnail_src( $post_id, $size = 'post-thumbnail' ) {
		$post_thumbnail_id = get_post_thumbnail_id( $post_id );

		if ( empty( $post_thumbnail_id ) ) {
			return false;
		}

		$post_thumbnail = wp_get_attachment_image_src( $post_thumbnail_id, $size );

		// Return the image src url
		return $post_thumbnail[0];
	}
endif;

if ( ! function_exists( 'make_timber_image_lazy' ) ) :
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
endif;
