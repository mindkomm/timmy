<?php

if ( ! function_exists( 'get_timber_image' ) ) :
/**
 * Outputs the src attr together with optional alt and title attributes
 * for a TimberImage.
 *
 * @param  int|TimberImage  $timber_image   Instance of TimberImage or Attachment ID
 * @param  string           $size           The size which you want to access
 * @return string                           Src, alt and title attributes
 */
function get_timber_image( $timber_image, $size ) {
	// When we just have the post id, we convert it to a TimberImage
	if ( is_numeric( $timber_image ) ) {
		$timber_image = new TimberImage( $timber_image );
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
 * @param  TimberImage|int  $timber_image   Instance of TimberImage or Attachment ID
 * @param  string           $size           Size key of the image to return
 * @return string                           Image src
 */
function get_timber_image_src( $timber_image, $size ) {
	// When we just have the post id, we convert it to a TimberImage
	if ( is_numeric( $timber_image ) ) {
		$timber_image = new TimberImage( $timber_image );
	}

	$img_sizes = get_image_sizes();

	$file_src  = $timber_image->get_src();
	$resize    = $img_sizes[ $size ]['resize'];

	$width  = $resize[0];
	$height = isset( $resize[1] ) ? $resize[1] : 0;
	$crop   = isset( $resize[2] ) ? $resize[2] : 'default';
	$force  = isset( $resize[3] ) ? $resize[3] : false;

	// Resize the image for that size
	return Timmy::resize( $img_sizes[$size], $file_src, $width, $height, $crop, $force );
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
 * @param  TimberImage  $timber_image   Instance of TimberImage
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
 * @param  TimberImage|int  $timber_image   Instance of TimberImage or Attachment ID
 * @param  string           $size           Size key of the image to return
 * @return string                           Image srcset, sizes, alt and title attributes
 */
function get_timber_image_responsive( $timber_image, $size ) {
	// When we just have the post id, we convert it to a TimberImage
	if ( is_numeric( $timber_image ) ) {
		$timber_image = new TimberImage( $timber_image );
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
 * @param  TimberImage|int  $timber_image   Instance of TimberImage or Attachment ID
 * @param  string           $size           Size key of the image to return
 * @return string                           Image srcset and sizes attributes
 */
function get_timber_image_responsive_src( $timber_image, $size ) {
	// When we just have the post id, we convert it to a TimberImage
	if ( is_numeric( $timber_image ) ) {
		$timber_image = new TimberImage( $timber_image );
	}

	$img_sizes = get_image_sizes();

	$file_src  = $timber_image->get_src();
	$resize    = $img_sizes[ $size ]['resize'];

	// Get values for the default image
	$width  = $resize[0];
	$height = isset( $resize[1] ) ? $resize[1] : 0;
	$crop   = isset( $resize[2] ) ? $resize[2] : 'default';
	$force  = isset( $resize[3] ) ? $resize[3] : false;

	$srcset = array();

	// Get proper width_key to handle width values of 0
	$width_key = Timmy::get_width_key( $width, $height, $timber_image );

	// We add the image sources with the width as the key so we can sort them later
	$srcset[ $width_key ] = Timmy::resize( $img_sizes[ $size ], $file_src, $width, $height, $crop, $force ) . ' ' . $width_key . 'w';

	// Add additional image sizes to srcset.
	if ( isset( $img_sizes[ $size ]['srcset'] ) ) {
		foreach ( $img_sizes[ $size ]['srcset'] as $src ) {

			// Get width and height for the additional src
			if ( is_array( $src ) ) {
				$width  = $src[0];
				$height = isset( $src[1] ) ? $src[1] : 0;
			} else {
				$width  = round( $resize[0] * $src );
				$height = isset( $resize[1] ) ? round( $resize[1] * $src ) : 0;
			}

			$width_key = Timmy::get_width_key( $width, $height, $timber_image );

			// For the new source, we use the same $crop and $force values as the default image
			$srcset[ $width_key ] = Timmy::resize( $img_sizes[ $size ], $file_src, $width, $height, $crop, $force ) . ' ' . $width_key . 'w';
		}
	}

	// Sort entries from smallest to highest
	ksort( $srcset );

	// Build sizes attribute string
	$sizes_str = '';
	if ( isset( $img_sizes[ $size ]['size'] ) ) {
		$sizes_str = ' sizes="' . $img_sizes[ $size ]['size'] . '"';
	}

	// Return the html attribute string
	return ' srcset="' . implode( ',', $srcset ) . '"' . $sizes_str;
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
	$timber_image = new TimberImage( $image['id'] );

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

	$html = ' alt="' . $image['alt'] . '"';

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
 * @param  int		$post_id	The post id to get the thumbnail from
 * @param  string	$size   	Size key of the image to return
 * @return string				Image src together with alt and title attributes
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
 * @param	int		$post_id	The post id to get the thumbnail from
 * @param	int		$size		Size key of the image to return
 * @return 	string				Image src
 */
function get_post_thumbnail_src( $post_id, $size = 'post-thumbnail' ) {
	$post_thumbnail_id = get_post_thumbnail_id( $post_id );
	$post_thumbnail    = wp_get_attachment_image_src( $post_thumbnail_id, $size );

	// Return the image src url
	return $post_thumbnail[0];
}
endif;
