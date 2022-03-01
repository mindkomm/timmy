<?php

/**
 * Class TestUpscale
 */
class TestPicture extends TimmyUnitTestCase {
	public function test_picture() {
		$alt_text   = 'Burrito Wrap';
		$attachment = $this->create_image( [
			'alt'         => $alt_text,
			'description' => 'Burritolino',
		] );
		$result     = get_timber_picture_responsive( $attachment, 'picture' );

		$expected =  '<source srcset="' . $this->get_upload_url() . '/test-560x0-c-default.jpg 560w, ' . $this->get_upload_url() . '/test-1400x0-c-default.jpg 1400w" sizes="100vw">' . PHP_EOL .
		'<img src="' . $this->get_upload_url() . '/test-1400x0-c-default.jpg" width="1400" height="933" alt="Burrito Wrap" loading="lazy">';

		$this->assertEquals( $expected, $result );
	}

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
}
