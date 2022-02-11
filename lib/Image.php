<?php

namespace Timmy;

use Timber\Image as TimberImage;

/**
 * Class Image
 *
 * @since 1.0.0
 */
class Image {
	/**
	 * Instance of Timber Image.
	 *
	 * @var mixed|\Timber\Image
	 */
	protected $timber_image;

	/**
	 * Image size.
	 *
	 * @var
	 */
	protected $size;


	protected $src;

	protected $meta;

	protected $max_width;

	protected $max_height;

	protected $upscale;

	/**
	 * @param $timber_image
	 */
	public function __construct( $timber_image, $size = null ) {
		if ( is_numeric( $timber_image ) ) {
			$timber_image = new TimberImage( $timber_image );
		} elseif ( is_array( $timber_image ) && isset( $timber_image['ID'] ) ) {
			// Convert an ACF image array into a Timber image.
			$timber_image = new TimberImage( $timber_image['ID'] );
		}

		$this->timber_image = $timber_image;
		$this->size = Helper::get_image_size( $size );

		$this->upscale = Helper::get_upscale_for_size( $this->size );
	}

	public function timber_image() {
		return $this->timber_image;
	}

	protected function load_attachment_image_src() {
		if ( empty( $this->src ) ) {
			list(
				$this->src,
				$this->max_width,
				$this->max_height
			) = wp_get_attachment_image_src( $this->timber_image->ID, 'full' );
		}
	}

	protected function load_attachment_meta_data() {
		if ( empty( $this->meta ) ) {
			/**
			 * Gets meta data not filtered by Timmy.
			 *
			 * @todo: Add a PR to Timber repository that saves the width and the height of an image in the
			 *      metadata. Timber already calls wp_get_attachment_metadata(), but discards the width and
			 *      height.
			 */
			$this->meta = wp_get_attachment_metadata( $this->timber_image->ID, true );
		}
	}

	/**
	 * Gets full file src.
	 *
	 * @return string
	 */
	public function src() {
		$this->load_attachment_image_src();

		return $this->src;
	}

	/**
	 * Returns a fallback for the src attribute to provide valid image markup and prevent double
	 * downloads in older browsers.
	 *
	 * @link http://scottjehl.github.io/picturefill/#support
	 *
	 * @param int|\Timber\Image $timber_image Instance of Timber\Image or attachment ID.
	 * @param string|array      $size         Size key or array of the image to return.
	 *
	 * @return string|bool Image src. False if image can’t be found or no srcset is available.
	 */
	public function src_default() {
		/**
		 * Filters whether a default src attribute should be added as a fallback.
		 *
		 * If this filter returns `true` (the default), then a base64 string will be used as a fallback
		 * to prevent double downloading images in older browsers. If this filter returns `false`, then
		 * no src attribute will be added to the image. Use the `timmy/src_default` filter to define
		 * what should be used as the src attribute’s value.
		 *
		 * @param bool $use_src_default Whether to apply the fallback. Default true.
		 */
		$use_src_default = apply_filters( 'timmy/use_src_default', true );

		if ( ! $use_src_default ) {
			return false;
		}

		// Default fallback src.
		$src_default = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

		/**
		 * Filters the src default.
		 *
		 * @param string $src_default Src default. Default
		 *                            `data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7`.
		 * @param array  $attributes  {
		 *     An array of helpful attributes in the filter.
		 *
		 *     @type string        $default_src  The default src for the image.
		 *     @type \Timber\Image $timber_image Timber image instance.
		 *     @type string        $size         The requested image size.
		 *     @type array         $img_size     The image size configuration.
		 *     @type array         $attributes   Attributes for the image markup.
		 * }
		 */
		$src_default = apply_filters(
			'timmy/src_default',
			$src_default,
			[
				'timber_image' => $timber_image,
				'size'         => $size,
				'img_size'     => $img_size,
			]
		);

		return $src_default;
	}

	public function max_width() {
		if ( empty( $this->max_width ) ) {
			$this->load_attachment_meta_data();

			$this->max_width = $this->meta['width'];
		}

		return $this->max_width;
	}

	public function max_height() {
		if ( empty( $this->max_height ) ) {
			$this->load_attachment_meta_data();

			$this->max_height = $this->meta['height'];
		}

		return $this->max_height;
	}

	/**
	 * Gets the image width for a size.
	 *
	 * @return false|int False on error or image width.
	 */
	public function width() {
		list( $width, $height ) = Helper::get_dimensions_for_size( $this->size );
		list( $width ) = Helper::get_dimensions_upscale( $width, $height, [
			'upscale'    => Helper::get_upscale_for_size( $this->size ),
			'resize'     => $this->size['resize'],
			'max_width'  => $this->max_width(),
			'max_height' => $this->max_height(),
		] );

		return $width;
	}

	public function height() {
		list( $width, $height ) = Helper::get_dimensions_for_size( $this->size );

		$height = Helper::maybe_fix_height( $height, $width, $this->max_width(), $this->max_height() );

		list( , $height ) = Helper::get_dimensions_upscale( $width, $height, [
			'upscale'    => Helper::get_upscale_for_size( $this->size ),
			'resize'     => $this->size['resize'],
			'max_width'  => $this->max_width(),
			'max_height' => $this->max_height(),
		] );

		return $height;
	}

	public function loading() {
		if ( ! wp_lazy_loading_enabled( 'img', 'timmy' ) ) {
			return false;
		}

		$allowed_lazy_values = [ 'lazy', 'eager', 'auto' ];

		if ( $this->args['loading'] && in_array( $this->args['loading'], $allowed_lazy_values, true ) ) {
			return $this->args['loading'];
		}

		return false;
	}

	public function upscale() {
		 return $this->upscale;
	}

	public function style() {
		/**
		 * Set width or height in px as a style attribute to act as max-width and max-height
		 * and prevent the image to be displayed bigger than it is.
		 *
		 * Using a style attribute is better than using width and height attributes, because width
		 * and height attributes are presentational, which means that any CSS will have higher
		 * specificity. If you automatically stretch images to the full width using "width: 100%",
		 * there’s no way you can prevent these images from growing bigger than they should. With
		 * a style attributes, that works.
		 *
		 * @since 0.10.0
		 */
		if ( $this->upscale['style_attr'] ) {
			if ( 'width' === $this->upscale['style_attr'] && ! $this->args['attr_width'] ) {
				return 'width:' . $this->max_width . 'px;';
			} elseif ( 'height' === $this->upscale['style_attr'] && ! $this->args['attr_height'] ) {
				return 'height:' . $this->max_height . 'px;';
			}
		}

		return false;
	}

	public function set_args( $args ) {
		$this->args = $args;
	}

	public function is_svg() {
		return 'image/svg+xml' === $this->timber_image->post_mime_type;
	}
}
