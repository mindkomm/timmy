<?php

use Timmy\Timmy;

class TestWebP extends TimmyUnitTestCase {
	public function test_picture_webp() {
		$alt_text   = 'Burrito Wrap';
		$attachment = $this->create_image( [
			'alt'         => $alt_text,
			'description' => 'Burritolino',
		] );
		$result     = get_timber_picture_responsive( $attachment, 'picture-webp' );

		$expected = '<source type="image/webp" srcset="' . $this->get_upload_url() . '/test-560x0-c-default.webp 560w, ' . $this->get_upload_url() . '/test-1400x0-c-default.webp 1400w" sizes="100vw">' . PHP_EOL . '<source type="image/jpeg" srcset="' . $this->get_upload_url() . '/test-560x0-c-default.jpg 560w, ' . $this->get_upload_url() . '/test-1400x0-c-default.jpg 1400w" sizes="100vw">' . PHP_EOL .  '<img src="' . $this->get_upload_url() . '/test-1400x0-c-default.jpg" width="1400" height="933" alt="Burrito Wrap" loading="lazy">';

		$this->assertEquals( $expected, $result );
	}

	public function test_picture_webp_with_small_image() {
		$attachment = $this->create_image( [
			'file' => 'test-200px.jpg',
		] );
		$result     = get_timber_picture_responsive( $attachment, 'picture-webp-with-small-image' );

		$expected = '<source type="image/webp" srcset="' . $this->get_upload_url() . '/test-200px-200x0-c-default.webp">' . PHP_EOL . '<source type="image/jpeg" srcset="' . $this->get_upload_url() . '/test-200px-200x0-c-default.jpg">' . PHP_EOL . '<img src="' . $this->get_upload_url() . '/test-200px-200x0-c-default.jpg" width="200" height="133" alt="" loading="lazy">';

		$this->assertEquals( $expected, $result );
	}

	public function test_picture_webp_with_small_image_square() {
		$attachment = $this->create_image( [
			'file' => 'test-200px.jpg',
		] );
		$result     = get_timber_picture_responsive( $attachment, 'picture-webp-resize-square' );

		$expected = '<source type="image/webp" srcset="' . $this->get_upload_url() . '/test-200px-133x133-c-default.webp">' . PHP_EOL . '<source type="image/jpeg" srcset="' . $this->get_upload_url() . '/test-200px-133x133-c-default.jpg">' . PHP_EOL .  '<img src="' . $this->get_upload_url() . '/test-200px-133x133-c-default.jpg" width="133" height="133" alt="" loading="lazy">';

		$this->assertEquals( $expected, $result );
	}

	public function test_picture_webp_args_array_with_srcset_descriptors() {
		$attachment = $this->create_image();
		$result     = get_timber_picture_responsive( $attachment, [
			'resize' => [ 260 ],
			'srcset' => [ '1x', '2x' ],
			'webp'   => true,
		] );

		$expected = '<source type="image/webp" srcset="' . $this->get_upload_url() . '/test-260x0-c-default.webp 1x, ' . $this->get_upload_url() . '/test-520x0-c-default.webp 2x">' . PHP_EOL . '<source type="image/jpeg" srcset="' . $this->get_upload_url() . '/test-260x0-c-default.jpg 1x, ' . $this->get_upload_url() . '/test-520x0-c-default.jpg 2x">' . PHP_EOL . '<img src="' . $this->get_upload_url() . '/test-260x0-c-default.jpg" width="260" height="173" alt="" loading="lazy">';

		$this->assertEquals( $expected, $result );
	}

	public function test_picture_webp_args_array_with_srcset_descriptors_timmy_image() {
		$attachment = $this->create_image();
		$image      = Timmy::get_image( $attachment, [
			'resize' => [ 260 ],
			'srcset' => [ '1x', '2x' ],
			'webp'   => true,
		] );

		$result = $image->picture_responsive();

		$expected = '<source type="image/webp" srcset="' . $this->get_upload_url() . '/test-260x0-c-default.webp 1x, ' . $this->get_upload_url() . '/test-520x0-c-default.webp 2x">' . PHP_EOL . '<source type="image/jpeg" srcset="' . $this->get_upload_url() . '/test-260x0-c-default.jpg 1x, ' . $this->get_upload_url() . '/test-520x0-c-default.jpg 2x">' . PHP_EOL . '<img src="' . $this->get_upload_url() . '/test-260x0-c-default.jpg" width="260" height="173" alt="" loading="lazy">';

		$this->assertEquals( $expected, $result );
	}

	/**
	 * Checks whether the large image metadata always returns a non-WebP image even if WebP is
	 * activated.
	 *
	 * @return void
	 */
	public function test_webp_not_generated_in_image_metadata() {
		// Make sure WebP is activated for the large image size.
		$this->add_filter_temporarily( 'timmy/sizes', function( $sizes ) {
			$sizes['large']['webp'] = true;

			return $sizes;
		} );

		$attachment = $this->create_image();
		$metadata   = wp_get_attachment_metadata( $attachment->ID );

		$this->assertEquals( 'image/jpeg', $metadata['sizes']['large']['mime-type'] );
	}
}
