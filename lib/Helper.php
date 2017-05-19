<?php

namespace Timmy;

class Helper {
	/**
	 * When an image is requested without a size name or with dimensions only, try to return the thumbnail.
	 * Otherwise take the first image in the image array.
	 */
	public static function get_thumbnail_size( $img_sizes ) {
		if ( isset( $img_sizes['thumbnail'] ) ) {
			return $img_sizes['thumbnail'];
		}

		return $img_size = reset( $img_sizes );
	}
}
