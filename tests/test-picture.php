<?php

use Timmy\Timmy;

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

	public function test_picture_loading_false() {
		$attachment = $this->create_image();
		$result     = get_timber_picture_responsive( $attachment, 'picture', [ 'loading' => false ] );

		$expected =  '<source srcset="' . $this->get_upload_url() . '/test-560x0-c-default.jpg 560w, ' . $this->get_upload_url() . '/test-1400x0-c-default.jpg 1400w" sizes="100vw">' . PHP_EOL .
		'<img src="' . $this->get_upload_url() . '/test-1400x0-c-default.jpg" width="1400" height="933" alt="">';

		$this->assertEquals( $expected, $result );
	}

	public function test_picture_loading_false_timmy_image() {
		$attachment = $this->create_image();

		$image  = Timmy::get_image( $attachment->ID, 'picture' );
		$result = $image->picture_responsive( [ 'loading' => false ] );

		$expected =  '<source srcset="' . $this->get_upload_url() . '/test-560x0-c-default.jpg 560w, ' . $this->get_upload_url() . '/test-1400x0-c-default.jpg 1400w" sizes="100vw">' . PHP_EOL .
		'<img src="' . $this->get_upload_url() . '/test-1400x0-c-default.jpg" width="1400" height="933" alt="">';

		$this->assertEquals( $expected, $result );
	}
}
