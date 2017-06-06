<?php

use Timmy\Timmy;
use Timmy\Helper;

/**
 * Frontend functions for Timmy.
 *
 * These functions are all pluggable, which means you can overwrite them if you add them to the functions.php file
 * of your theme.
 */

if ( ! function_exists( 'get_timber_image' ) ) :
	/**
	 * Outputs the src attr together with optional alt and title attributes for a TimberImage.
	 *
	 * @param  int|Timber\Image $timber_image Instance of TimberImage or Attachment ID.
	 * @param  string           $size         The size which you want to access.
	 * @return string|bool Src, alt and title attributes. False if image can’t be found.
	 */
	function get_timber_image( $timber_image, $size ) {
		$src  = get_timber_image_src( $timber_image, $size );

		if ( ! $src ) {
			return false;
		}

		$attr = get_timber_image_attr( $timber_image );

		return ' src="' . $src . '" ' . $attr;
	}
endif;

if ( ! function_exists( 'get_timber_image_src' ) ) :
	/**
	 * Returns the src (url) for a TimberImage.
	 *
	 * @param  int|Timber\Image $timber_image Instance of TimberImage or Attachment ID.
	 * @param  string           $size         Size key of the image to return.
	 * @return string|bool Image src. False if image can’t be found.
	 */
	function get_timber_image_src( $timber_image, $size ) {
		$timber_image = Timmy::get_timber_image( $timber_image );

		if ( ! $timber_image ) {
			return false;
		}

		// Directly return full source when an SVG image is requested.
		if ( 'image/svg+xml' === $timber_image->post_mime_type ) {
			return wp_get_attachment_url( $timber_image->ID );
		}

		$img_size = Helper::get_image_size( $size );

		if ( ! $img_size ) {
			return false;
		}

		list(
			$file_src,
			$width,
			$height,
			$crop,
			$force,
		) = Timmy::get_image_params( $timber_image, $img_size );

		// Resize the image for that size
		return Timmy::resize( $img_size, $file_src, $width, $height, $crop, $force );
	}
endif;

if ( ! function_exists( 'get_image_attr_html' ) ) :
	/**
	 * Returns the HTML for given alt and title attribute strings.
	 *
	 * This will always include the alt tag. For accessibility, the
	 * alt tag needs to be included even if it is empty.
	 *
	 * @param  string $alt   Alt text.
	 * @param  string $title Title text.
	 * @return string HTML string for alt and title attributes.
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
	 * @param  Timber\Image $timber_image Instance of TimberImage.
	 * @return string HTML string for alt and title attributes. False if image can’t be found.
	 */
	function get_timber_image_attr( $timber_image ) {
		$timber_image = Timmy::get_timber_image( $timber_image );

		if ( ! $timber_image ) {
			return false;
		}

		$alt   = $timber_image->_wp_attachment_image_alt;
		$title = $timber_image->post_content;

		return get_image_attr_html( $alt, $title );
	}
endif;

if ( ! function_exists( 'get_timber_image_responsive' ) ) :
	/**
	 * Get the responsive srcset and sizes for a TimberImage.
	 *
	 * @param Timber\Image|int $timber_image Instance of TimberImage or Attachment ID.
	 * @param string           $size         Size key of the image to return.
	 * @param array            $args {
	 *      Optional. Array of options.
	 *
	 *      @type bool $attr_width  Whether to add a width attribute to an image, if needed. Default false.
	 *      @type bool $attr_height Whether to add a height attribute to an image, if need. Default false.
	 * }
	 * @return string|bool                    Image srcset, sizes, alt and title attributes. False if image
	 *                                        can’t be found.
	 */
	function get_timber_image_responsive( $timber_image, $size, $args = array() ) {
		$src = get_timber_image_responsive_src( $timber_image, $size, $args );

		if ( ! $src ) {
			return false;
		}

		$attr = get_timber_image_attr( $timber_image );

		return $src . ' ' . $attr;
	}
endif;

if ( ! function_exists( 'get_timber_image_responsive_src' ) ) :
	/**
	 * Get srcset and sizes for a TimberImage.
	 *
	 * @param Timber\Image|int $timber_image Instance of TimberImage or Attachment ID.
	 * @param string           $size         Size key of the image to return.
	 * @param array            $args {
	 *      Optional. Array of options.
	 *
	 *      @type bool $attr_width  Whether to add a width attribute to an image, if needed. Default false.
	 *      @type bool $attr_height Whether to add a height attribute to an image, if need. Default false.
	 * }
	 * @return string|bool Image srcset and sizes attributes. False if image can’t be found.
	 */
	function get_timber_image_responsive_src( $timber_image, $size, $args = array() ) {
		$timber_image = Timmy::get_timber_image( $timber_image );

		if ( ! $timber_image ) {
			return false;
		}

		// Directly return full source when full source or an SVG image is requested.
		if ( 'full' === $size || 'image/svg+xml' === $timber_image->post_mime_type ) {
			return ' src="' . wp_get_attachment_url( $timber_image->ID ) . '"';
		}

		$img_size = Helper::get_image_size( $size );

		if ( ! $img_size ) {
			return false;
		}

		/**
		 * Default arguments for image markup
		 *
		 * @since 0.12.0
		 */
		$default_args = array(
			'attr_width' => false,
			'attr_height' => false,
		);

		$args = wp_parse_args( $args, $default_args );

		list(
			$file_src,
			$width,
			$height,
			$crop,
			$force,
			$max_width,
			$max_height,
			$oversize,
		) = Timmy::get_image_params( $timber_image, $img_size );

		$srcset = array();

		// Get proper width_key to handle width values of 0
		$width_key = Timmy::get_width_key( $width, $height, $timber_image );

		// Get default size for image
		$default_size = Timmy::resize( $img_size, $file_src, $width, $height, $crop, $force );

		// Add the image source with the width as the key so they can be sorted later.
		$srcset[ $width_key ] = $default_size . " {$width_key}w";

		// Add additional image sizes to srcset.
		if ( isset( $img_size['srcset'] ) ) {
			foreach ( $img_size['srcset'] as $srcset_src ) {
				list(
					$width_intermediate,
					$height_intermediate
				) = Helper::get_dimensions_for_srcset_size( $img_size['resize'], $srcset_src );

				// Bail out if the current size’s width is bigger than available width
				if ( ! $oversize['allow']
					 && ( $width_intermediate > $max_width
						 || ( 0 === $width_intermediate && $height_intermediate > $max_height )
					 )
				) {
					continue;
				}

				$width_key = Timmy::get_width_key( $width_intermediate, $height_intermediate, $timber_image );

				// For the new source, we use the same $crop and $force values as the default image
				$srcset[ $width_key ] = Timmy::resize(
					$img_size,
					$file_src,
					$width_intermediate,
					$height_intermediate,
					$crop,
					$force
				) . " {$width_key}w";
			}
		}

		// Attribute strings
		$attr_sizes = '';
		$attr_width = '';
		$attr_height = '';

		/**
		 * Check for 'sizes' option in image configuration.
		 * Before v0.10.0, this was just `size`'.
		 *
		 * @since 0.10.0
		 */
		if ( isset( $img_size['sizes'] ) ) {
			$attr_sizes = ' sizes="' . $img_size['sizes'] . '"';

		/**
		 * For backwards compatibility
		 * @deprecated since 0.10.0
		 */
		} elseif ( isset( $img_size['size'] ) ) {
			$attr_sizes = ' sizes="' . $img_size['size'] . '"';
		}

		/**
		 * Set width or height in px as a style attribute to act as max-width and max-height
		 * and prevent the image to be displayed bigger than it is.
		 *
		 * @since 0.10.0
		 */
		if ( $oversize['style_attr'] ) {
			if ( 'width' === $oversize['style_attr'] && ! $args['attr_width'] ) {
				$attr_width = ' style="width:' . $max_width . 'px;"';
			} elseif ( 'height' === $oversize['style_attr'] && ! $args['attr_height'] ) {
				$attr_height = ' style="height:' . $max_height . 'px;"';
			}
		}

		if ( $args['attr_width'] ) {
			$attr_width = ' width="' . $width . '"';
		}

		if ( $args['attr_height'] ) {
			$attr_height = ' height="' . $height . '"';
		}

		$html = '';

		/**
		 * Only add responsive srcset and sizes attributes if there are any present.
		 * If there’s only one srcset src, it’s always the default size. In that case, we just add it as a src.
		 */
		if ( count( $srcset ) > 1 ) {
			// Sort entries from smallest to highest
			ksort( $srcset );

			$html .= 'srcset="' . implode( ', ', $srcset ) . '"' . $attr_sizes;

			/**
			 * Add fallback for src attribute to provide valid image markup
			 * and prevent double downloads in older browsers.
			 *
			 * @link http://scottjehl.github.io/picturefill/#support
			 */
			$html .= ' src="data:image/gif;base64,R0lGODlhAQABAAAAADs="';
		} else {
			$html .= 'src="' . $default_size . '"';
		}

		$html .= $attr_width . $attr_height;

		return $html;
	}
endif;

if ( ! function_exists( 'get_timber_image_responsive_acf' ) ) :
	/**
	 * Get a responsive image based on an ACF field.
	 *
	 * @param  string $name ACF Field Name.
	 * @param  string $size Size key of the image to return.
	 * @return string|bool Image srcset, sizes, alt and title attributes. False if image can’t be found.
	 */
	function get_timber_image_responsive_acf( $name, $size ) {
		$image = get_field( $name );
		$timber_image = Timmy::get_timber_image( $image );

		if ( ! $timber_image ) {
			return false;
		}

		$src  = get_timber_image_responsive_src( $timber_image, $size );
		$attr = get_acf_image_attr( $image );

		return $src . ' ' . $attr;
	}
endif;

if ( ! function_exists( 'get_acf_image_attr' ) ) :
	/**
	 * Get image attributes for an image accessed via ACF.
	 *
	 * @param  array $image ACF Image.
	 * @return string Alt and title attribute.
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
	 * @param  int    $post_id The post id to get the thumbnail from.
	 * @param  string $size    Size key of the image to return.
	 * @return string|bool Image src together with alt and title attributes. False if no image can’t be found.
	 */
	function get_post_thumbnail( $post_id, $size = 'post-thumbnail' ) {
		$thumbnail_src = get_post_thumbnail_src( $post_id, $size );

		if ( ! $thumbnail_src ) {
			return false;
		}

		$html = ' src="' . $thumbnail_src . '"';

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
	 * @param  int    $post_id The post id to get the thumbnail from.
	 * @param  string $size    Size key of the image to return.
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
