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

		$expected =  '<source srcset="' . $this->get_upload_url() . '/test-560x0-c-default.jpg 560w, ' . $this->get_upload_url() . '/test-1400x0-c-default.jpg 1400w">' . PHP_EOL .
		'<img src="' . $this->get_upload_url() . '/test-1400x0-c-default.jpg" alt="Burrito Wrap">';

		$this->assertEquals( $expected, $result );
	}

	public function test_picture_webp() {
		$alt_text   = 'Burrito Wrap';
		$attachment = $this->create_image( [
			'alt'         => $alt_text,
			'description' => 'Burritolino',
		] );
		$result     = get_timber_picture_responsive( $attachment, 'picture-webp' );

		$expected = '<source type="image/jpeg" srcset="' . $this->get_upload_url() . '/test-560x0-c-default.jpg 560w, ' . $this->get_upload_url() . '/test-1400x0-c-default.jpg 1400w">' . PHP_EOL . '<source type="image/webp" srcset="' . $this->get_upload_url() . '/test-560x0-c-default.webp 560w, ' . $this->get_upload_url() . '/test-1400x0-c-default.webp 1400w">' . PHP_EOL . '<img src="' . $this->get_upload_url() . '/test-1400x0-c-default.jpg" alt="Burrito Wrap">';

		$this->assertEquals( $expected, $result );
	}
}
