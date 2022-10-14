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

		$expected = sprintf(
			'<source srcset="%1$s/test-560x0-c-default.jpg 560w, %1$s/test-1400x0-c-default.jpg 1400w" sizes="100vw">%2$s<img src="%1$s/test-1400x0-c-default.jpg" width="1400" height="933" alt="Burrito Wrap" loading="lazy">',
			$this->get_upload_url(),
			PHP_EOL
		);

		$this->assertEquals( $expected, $result );
	}

	public function test_picture_loading_false() {
		$attachment = $this->create_image();
		$result     = get_timber_picture_responsive( $attachment, 'picture', [ 'loading' => false ] );

		$expected = sprintf(
			'<source srcset="%1$s/test-560x0-c-default.jpg 560w, %1$s/test-1400x0-c-default.jpg 1400w" sizes="100vw">%2$s<img src="%1$s/test-1400x0-c-default.jpg" width="1400" height="933" alt="">',
			$this->get_upload_url(),
			PHP_EOL
		);

		$this->assertEquals( $expected, $result );
	}

	public function test_picture_loading_false_timmy_image() {
		$attachment = $this->create_image();

		$image  = Timmy::get_image( $attachment->ID, 'picture' );
		$result = $image->picture_responsive( [ 'loading' => false ] );

		$expected = sprintf(
			'<source srcset="%1$s/test-560x0-c-default.jpg 560w, %1$s/test-1400x0-c-default.jpg 1400w" sizes="100vw">%2$s<img src="%1$s/test-1400x0-c-default.jpg" width="1400" height="933" alt="">',
			$this->get_upload_url(),
			PHP_EOL
		);

		$this->assertEquals( $expected, $result );
	}


		$this->assertEquals( $expected, $result );
	}
}
