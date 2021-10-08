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
			' srcset="%1$s/test-560x0-c-default.jpg 560w, %1$s/test-1400x0-c-default.jpg 1400w" sizes="100vw" width="1400" height="933" loading="lazy" alt=""',
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
			' srcset="%1$s/test-560x0-c-default.jpg 560w, %1$s/test-1400x0-c-default.jpg 1400w" src="%1$s/test-1400x0-c-default.jpg" sizes="100vw" width="1400" height="933" loading="lazy" alt=""',
			$this->get_upload_url()
		);

		$this->assertEquals( $expected, $result );

		remove_filter( 'timmy/src_default', $callback );
	}

	/**
	 * @ticket https://github.com/mindkomm/timmy/issues/28
	 */
	function test_generate_srcset_sizes_active() {
		// Generate all sizes upon upload.
		add_filter( 'timmy/generate_srcset_sizes', '__return_true' );

		$sizes_filter = function( $sizes ) {
			return [
				'custom-4' => [
					'resize'     => [ 370 ],
					'srcset'     => [ 2 ],
					'sizes'      => '(min-width: 992px) 33.333vw, 100vw',
					'name'       => 'Width 1/4 fix',
					'post_types' => [ 'post', 'page' ],
				],
			];
		};

		add_filter( 'timmy/sizes', $sizes_filter );

		// Make sure all upload files are deleted.
		$this->delete_test_images();

		// Simulate image uploading.
		$post = $this->create_post_with_image();

		$path = $this->get_file_path( $post->thumbnail(), 'custom-4' );
		$path = str_replace( '370x0', 2 * 370 . 'x0', $path );

		$this->assertFileExists( $path );

		remove_filter( 'timmy/sizes', $sizes_filter );
		remove_filter( 'timmy/generate_srcset_sizes', '__return_true' );
	}
}
