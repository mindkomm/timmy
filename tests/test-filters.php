<?php

class TestFilters extends TimmyUnitTestCase {
	public function test_use_src_fallback_disable() {
		$this->add_filter_temporarily( 'timmy/use_src_default', '__return_false' );

		$attachment = $this->create_image();
		$result     = get_timber_image_responsive( $attachment, 'large' );

		$expected = sprintf(
			' srcset="%1$s/test-560x0-c-default.jpg 560w, %1$s/test-1400x0-c-default.jpg 1400w" sizes="100vw" width="1400" height="933" loading="lazy" alt=""',
			$this->get_upload_url()
		);

		$this->assertEquals( $expected, $result );
	}

	public function test_src_default() {
		$callback = function( $src_default, $attributes ) {
			return 'alternative_src_default';
		};

		$this->add_filter_temporarily( 'timmy/src_default', $callback, 10, 2 );

		$attachment = $this->create_image();
		$result     = get_timber_image_responsive( $attachment, 'large' );

		$expected = sprintf(
			' srcset="%1$s/test-560x0-c-default.jpg 560w, %1$s/test-1400x0-c-default.jpg 1400w" sizes="100vw" src="alternative_src_default" width="1400" height="933" loading="lazy" alt=""',
			$this->get_upload_url()
		);

		$this->assertEquals( $expected, $result );
	}

	public function test_image_class_arguments() {
		$received = [];

		$callback = function( $class, $attachment_id, $size_array, $size ) use ( &$received ) {
			$received[] = func_get_args();

			return $class;
		};

		$this->add_filter_temporarily( 'timmy/image/class', $callback, 10, 4 );

		$attachment = $this->create_image();

		\Timmy\Timmy::get_image( $attachment, 'large' );

		$this->assertCount( 1, $received );
		$this->assertEquals( \Timmy\Image::class, $received[0][0] );
		$this->assertSame( $attachment->ID, $received[0][1] );
		$this->assertEquals( \Timmy\Helper::get_image_size( 'large' ), $received[0][2] );
		$this->assertEquals( 'large', $received[0][3] );
	}

	public function test_image_class_arguments_for_full_size() {
		$received = [];

		$callback = function( $class, $attachment_id, $size_array, $size ) use ( &$received ) {
			$received[] = func_get_args();

			return $class;
		};

		$this->add_filter_temporarily( 'timmy/image/class', $callback, 10, 4 );

		$attachment = $this->create_image();

		\Timmy\Timmy::get_image( $attachment, 'full' );

		$this->assertCount( 1, $received );
		$this->assertSame( $attachment->ID, $received[0][1] );
		$this->assertEquals( [], $received[0][2] );
		$this->assertEquals( 'full', $received[0][3] );
	}

	/**
	 * Makes sure that callbacks that were registered before the additional arguments were
	 * introduced still work.
	 */
	public function test_image_class_backwards_compatibility() {
		$this->add_filter_temporarily( 'timmy/image/class', function( $class ) {
			return TestFiltersImage::class;
		} );

		$attachment = $this->create_image();
		$image      = \Timmy\Timmy::get_image( $attachment, 'large' );

		$this->assertInstanceOf( TestFiltersImage::class, $image );
	}

	/**
	 * Makes it possible to select a class per attachment.
	 */
	public function test_image_class_per_attachment() {
		$attachment_one = $this->create_image();
		$attachment_two = $this->create_image( [ 'file' => 'test-200px.jpg' ] );

		$this->add_filter_temporarily( 'timmy/image/class', function( $class, $attachment_id ) use ( $attachment_two ) {
			return $attachment_id === $attachment_two->ID ? TestFiltersImage::class : $class;
		}, 10, 2 );

		$image_one = \Timmy\Timmy::get_image( $attachment_one, 'large' );
		$image_two = \Timmy\Timmy::get_image( $attachment_two, 'large' );

		$this->assertNotInstanceOf( TestFiltersImage::class, $image_one );
		$this->assertInstanceOf( TestFiltersImage::class, $image_two );
	}

	/**
	 * @ticket https://github.com/mindkomm/timmy/issues/28
	 */
	function test_generate_srcset_sizes_active() {
		// Generate all sizes upon upload.
		$this->add_filter_temporarily( 'timmy/generate_srcset_sizes', '__return_true' );

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

		$this->add_filter_temporarily( 'timmy/sizes', $sizes_filter );

		// Make sure all upload files are deleted.
		$this->delete_test_images();

		// Simulate image uploading.
		$post = $this->create_post_with_image();

		$path = $this->get_file_path( $post->thumbnail(), 'custom-4' );
		$path = str_replace( '370x0', 2 * 370 . 'x0', $path );

		$this->assertFileExists( $path );
	}
}

/**
 * Image class used to test the `timmy/image/class` filter.
 */
class TestFiltersImage extends \Timmy\Image {
}
