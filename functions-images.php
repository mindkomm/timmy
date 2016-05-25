<?php

use Timmy\Timmy;

if ( ! function_exists( 'get_timber_image' ) ) :
/**
 * Outputs the src attr together with optional alt and title attributes
 * for a TimberImage.
 *
 * @param  int|Timber\Image $timber_image   Instance of TimberImage or Attachment ID
 * @param  string           $size           The size which you want to access
 * @return string                           Src, alt and title attributes
 */
function get_timber_image( $timber_image, $size ) {
	// When we just have the post id, we convert it to a TimberImage
	if ( is_numeric( $timber_image ) ) {
		$timber_image = new Timber\Image( $timber_image );
	}

	$src  = get_timber_image_src( $timber_image, $size );
	$attr = get_timber_image_attr( $timber_image );

	return ' src="' . $src . '" ' . $attr;
}
endif;

if ( ! function_exists( 'get_timber_image_src' ) ) :
/**
 * Returns the src (url) for a TimberImage.
 *
 * @param  int|Timber\Image $timber_image   Instance of TimberImage or Attachment ID
 * @param  string           $size           Size key of the image to return
 * @return string                           Image src
 */
function get_timber_image_src( $timber_image, $size ) {
	// When we just have the post id, we convert it to a TimberImage
	if ( is_numeric( $timber_image ) ) {
		$timber_image = new Timber\Image( $timber_image );
	}

	$img_sizes = get_image_sizes();

	list(
		$file_src,
		$width,
		$height,
		$crop,
		$force,
	) = Timmy::get_image_params( $timber_image, $img_sizes[ $size ] );

	// Resize the image for that size
	return Timmy::resize( $img_sizes[ $size ], $file_src, $width, $height, $crop, $force );
}
endif;

if ( ! function_exists( 'get_image_attr_html' ) ) :
/**
 * Returns the HTML for given alt and title attribute strings.
 *
 * This will always include the alt tag. For accessibility, the
 * alt tag needs to be included even if it is empty.
 *
 * @param  string $alt   Alt text
 * @param  string $title Title text
 * @return string        HTML string for alt and title attributes
 */
function get_image_attr_html( $alt, $title ) {
	$html = ' alt="' . $alt . '"';

	if ( ! empty( $title ) ) {
		$html .= ' title="' . $title . '"';
	}

	return $html;
}
endif;

if ( ! function_exists( 'get_timber_image_attr' ) ) :
/**
 * Get the image attributes (alt and title) for a TimberImage.
 *
 * @param  Timber\Image  $timber_image  Instance of TimberImage
 * @return string                       HTML string for alt and title attributes
 */
function get_timber_image_attr( $timber_image ) {
	$alt   = $timber_image->_wp_attachment_image_alt;
	$title = $timber_image->post_content;
	return get_image_attr_html( $alt, $title );
}
endif;

if ( ! function_exists( 'get_timber_image_responsive' ) ) :
/**
 * Get the responsive srcset and sizes for a TimberImage.
 *
 * @param  Timber\Image|int  $timber_image  Instance of TimberImage or Attachment ID
 * @param  string           $size           Size key of the image to return
 * @return string                           Image srcset, sizes, alt and title attributes
 */
function get_timber_image_responsive( $timber_image, $size ) {
	// When we just have the post id, we convert it to a TimberImage
	if ( is_numeric( $timber_image ) ) {
		$timber_image = new Timber\Image( $timber_image );
	}

	$src = get_timber_image_responsive_src( $timber_image, $size );
	$attr = get_timber_image_attr( $timber_image );

	return $src . ' ' . $attr;
}
endif;

if ( ! function_exists( 'get_timber_image_responsive_src' ) ) :
/**
 * Get srcset and sizes for a TimberImage.
 *
 * @param  Timber\Image|int  $timber_image  Instance of TimberImage or Attachment ID
 * @param  string           $size           Size key of the image to return
 * @return string                           Image srcset and sizes attributes
 */
function get_timber_image_responsive_src( $timber_image, $size ) {
	// When we just have the post id, we convert it to a TimberImage
	if ( is_numeric( $timber_image ) ) {
		$timber_image = new Timber\Image( $timber_image );
	}

	$img_sizes = get_image_sizes();
	$resize    = $img_sizes[ $size ]['resize'];

	list(
		$file_src,
		$width,
		$height,
		$crop,
		$force,
		$max_width,
		$max_height,
		$oversize,
	) = Timmy::get_image_params( $timber_image, $img_sizes[ $size ] );

	$srcset = array();

	// Get proper width_key to handle width values of 0
	$width_key = Timmy::get_width_key( $width, $height, $timber_image );

	// Add the image source with the width as the key so they can be sorted later.
	$srcset[ $width_key ] = Timmy::resize( $img_sizes[ $size ], $file_src, $width, $height, $crop, $force ) . ' ' . $width_key . 'w';

	// Add additional image sizes to srcset.
	if ( isset( $img_sizes[ $size ]['srcset'] ) ) {
		foreach ( $img_sizes[ $size ]['srcset'] as $src ) {

			// Get width and height for the additional src
			if ( is_array( $src ) ) {
				$width  = $src[0];
				$height = isset( $src[1] ) ? $src[1] : 0;
			} else {
				$width  = (int) round( $resize[0] * $src );
				$height = isset( $resize[1] ) ? (int) round( $resize[1] * $src ) : 0;
			}

			// Bail out if the current size’s width is bigger than available width
			if ( ! $oversize['allow'] && ( $width > $max_width || ( 0 === $width && $height > $max_height ) ) ) {
				continue;
			}

			$width_key = Timmy::get_width_key( $width, $height, $timber_image );

			// For the new source, we use the same $crop and $force values as the default image
			$srcset[ $width_key ] = Timmy::resize( $img_sizes[ $size ], $file_src, $width, $height, $crop, $force ) . ' ' . $width_key . 'w';
		}
	}

	// Sort entries from smallest to highest
	ksort( $srcset );

	// Build sizes attribute string
	$attr_str = '';

	/**
	 * Check for 'sizes' option in image configuration.
	 * Before v0.10.0, this was just `sizes'.
	 *
	 * @since 0.10.0
	 */
	if ( isset( $img_sizes[ $size ]['sizes'] ) ) {
		$attr_str = ' sizes="' . $img_sizes[ $size ]['sizes'] . '"';

	/**
	 * For backwards compatibility
	 * @deprecated since 0.10.0
	 */
	} else if ( isset( $img_sizes[ $size ]['size'] ) ) {
		$attr_str = ' sizes="' . $img_sizes[ $size ]['size'] . '"';
	}

	/**
	 * Set max-width|max-height in px to prevent the image to be displayed bigger than it is
	 *
	 * @since 0.10.0
	 */
	if ( ! $oversize['allow'] && $oversize['style_attr'] ) {
		if ( 'width' === $oversize['style_attr'] ) {
			$attr_str = ' style="width:' . $max_width . 'px;"';
		} else if ( 'height' === $oversize['style_attr'] ) {
			$attr_str = ' style="height:' . $max_height . 'px;"';
		}
	}

	// Return the HTML attribute string
	return ' srcset="' . implode( ', ', $srcset ) . '"' . $attr_str;
}
endif;

if ( ! function_exists( 'get_timber_image_responsive_acf' ) ) :
/**
 * Get a responsive image based on an ACF field.
 *
 * @param  string	$name	ACF Field Name
 * @param  string	$size	Size key of the image to return
 * @return string       	Image srcset, sizes, alt and title attributes
 */
function get_timber_image_responsive_acf( $name, $size ) {
	$image = get_field( $name );
	$timber_image = new Timber\Image( $image['id'] );

	$src  = get_timber_image_responsive_src( $timber_image, $size );
	$attr = get_acf_image_attr( $image );

	return $src . ' ' . $attr;
}
endif;

if ( ! function_exists( 'get_acf_image_attr' ) ) :
/**
 * Get image attributes for an image accessed via ACF.
 *
 * @param  array	$image	ACF Image
 * @return string    		alt and title attribute
 */
function get_acf_image_attr( $image ) {
	$alt = ! empty( $image['alt'] ) ? $image['alt'] : '';

	$html = ' alt="' . $alt . '"';

	if ( ! empty( $image['description'] ) ) {
		$html .= ' title="' . $image['description'] . '"';
	}

	return $html;
}
endif;

if ( ! function_exists( 'get_post_thumbnail' ) ) :
/**
 * Get Post Thumbnail source together with alt and title attributes.
 *
 * @param  int      $post_id    The post id to get the thumbnail from
 * @param  string   $size       Size key of the image to return
 * @return string               Image src together with alt and title attributes
 */
function get_post_thumbnail( $post_id, $size = 'post-thumbnail' ) {
	$html = ' src="' . get_post_thumbnail_src( $post_id, $size ) . '"';

	$thumb_id   = get_post_thumbnail_id( $post_id );
	$attachment = get_post( $thumb_id );

	// Alt attributes are saved as post meta
	$alt = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );

	// We take the image description for the title
	$title = $attachment->post_content;

	$html .= get_image_attr_html( $alt, $title );

	return $html;
}
endif;

if ( ! function_exists( 'get_post_thumbnail_src' ) ) :
/**
 * Get Post Thumbnail image source at given size.
 *
 * @param   int     $post_id    The post id to get the thumbnail from
 * @param   string  $size       Size key of the image to return
 * @return  string              Image src
 */
function get_post_thumbnail_src( $post_id, $size = 'post-thumbnail' ) {
	$post_thumbnail_id = get_post_thumbnail_id( $post_id );
	$post_thumbnail    = wp_get_attachment_image_src( $post_thumbnail_id, $size );

	// Return the image src url
	return $post_thumbnail[0];
}
endif;
