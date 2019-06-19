<?php

use Timber\ImageHelper;
use Timber\Timber;

/**
 * Sample test case.
 */
class TestTimmy extends TimmyUnitTestCase {
	/**
	 * A single example test.
	 */
	public function test_sample() {
		$this->assertTrue( true );
	}

	public function test_create_image() {
		$attachment = $this->create_image();

		$src  = Timber::compile_string( '{{ img.src }}', [ 'img' => $attachment ] );
		$path = ImageHelper::get_server_location( $src );

		$this->assertFileExists( $path );
	}
}
