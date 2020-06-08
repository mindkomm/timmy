<?php

/**
 * Class TestFilters
 */
class TestFilters extends TimmyUnitTestCase {
	public function test_use_src_fallback_disable() {
		add_filter( 'timmy/use_src_default', '__return_false' );

		$attachment = $this->create_image();
		$result     = get_timber_image_responsive( $attachment, 'large' );

		$expected = sprintf(
			' srcset="%1$s/test-560x0-c-default.jpg 560w, %1$s/test-1400x0-c-default.jpg 1400w" sizes="100vw" alt=""',
			$this->get_upload_url()
		);

		$this->assertEquals( $expected, $result );

		remove_filter( 'timmy/use_src_default', '__return_false' );
	}

	public function test_src_default() {
		$callback = function( $src_default, $attributes ) {
			return $attributes['default_src'];
		};

		add_filter( 'timmy/src_default', $callback, 10, 2 );

		$attachment = $this->create_image();
		$result     = get_timber_image_responsive( $attachment, 'large' );

		$expected = sprintf(
			' srcset="%1$s/test-560x0-c-default.jpg 560w, %1$s/test-1400x0-c-default.jpg 1400w" src="%1$s/test-1400x0-c-default.jpg" sizes="100vw" alt=""',
			$this->get_upload_url()
		);

		$this->assertEquals( $expected, $result );

		remove_filter( 'timmy/src_default', $callback );
	}
}
