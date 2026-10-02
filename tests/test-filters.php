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

	public function test_image_url() {
		$this->add_filter_temporarily( 'timmy/use_src_default', '__return_false' );
		$this->add_filter_temporarily( 'timmy/image/url', function( $url, $width, $height ) {
			return sprintf( 'https://cdn.example.com/%d/%d/image.jpg', $width, $height );
		}, 10, 3 );

		$attachment = $this->create_image();
		$result     = get_timber_image_responsive( $attachment, 'large' );

		$expected = ' srcset="https://cdn.example.com/560/0/image.jpg 560w, https://cdn.example.com/1400/0/image.jpg 1400w" sizes="100vw" width="1400" height="933" loading="lazy" alt=""';

		$this->assertEquals( $expected, $result );
	}

	public function test_image_url_arguments() {
		$received = [];

		$this->add_filter_temporarily( 'timmy/image/url', function( $url, $width, $height, $webp, $image ) use ( &$received ) {
			$received[] = [ $url, $width, $height, $webp, $image ];

			return 'https://cdn.example.com/image.jpg';
		}, 10, 5 );

		$attachment = $this->create_image();

		get_timber_image_src( $attachment, 'large' );

		$this->assertCount( 1, $received );
		$this->assertNull( $received[0][0] );
		$this->assertEquals( 1400, $received[0][1] );
		$this->assertEquals( 0, $received[0][2] );
		$this->assertFalse( $received[0][3] );
		$this->assertInstanceOf( \Timmy\Image::class, $received[0][4] );
		$this->assertSame( $attachment->ID, $received[0][4]->id );
	}

	public function test_image_url_webp_argument() {
		$received = [];

		$this->add_filter_temporarily( 'timmy/image/url', function( $url, $width, $height, $webp ) use ( &$received ) {
			$received[] = $webp;

			return 'https://cdn.example.com/image.webp';
		}, 10, 4 );

		$attachment = $this->create_image();
		$result     = get_timber_image_src( $attachment, 'webp' );

		$this->assertEquals( [ true ], $received );

		// The URL is used as it is. Timmy doesn’t append a .webp extension to it.
		$this->assertEquals( 'https://cdn.example.com/image.webp', $result );
	}

	public function test_image_url_null_falls_back_to_resizing() {
		$called = 0;

		$this->add_filter_temporarily( 'timmy/image/url', function( $url ) use ( &$called ) {
			$called++;

			return $url;
		} );

		$this->add_filter_temporarily( 'timmy/use_src_default', '__return_false' );

		$attachment = $this->create_image();
		$result     = get_timber_image_responsive( $attachment, 'large' );

		$expected = sprintf(
			' srcset="%1$s/test-560x0-c-default.jpg 560w, %1$s/test-1400x0-c-default.jpg 1400w" sizes="100vw" width="1400" height="933" loading="lazy" alt=""',
			$this->get_upload_url()
		);

		$this->assertEquals( 2, $called );
		$this->assertEquals( $expected, $result );
	}

	/**
	 * Makes sure that no image files are generated when the URL is provided through the filter.
	 */
	public function test_image_url_does_not_generate_files() {
		$attachment = $this->create_image();

		// Srcset sizes are not generated when an image is uploaded.
		$srcset_file = $this->get_upload_path() . '/test-560x0-c-default.jpg';

		$this->assertFileDoesNotExist( $srcset_file );

		$this->add_filter_temporarily( 'timmy/image/url', function() {
			return 'https://cdn.example.com/image.jpg';
		} );

		get_timber_image_responsive( $attachment, 'large' );

		$this->assertFileDoesNotExist( $srcset_file );
	}

	/**
	 * Makes sure that the filter is also applied when the original image already has the
	 * requested width and no resizing is needed.
	 */
	public function test_image_url_for_original_width() {
		$this->add_filter_temporarily( 'timmy/image/url', function() {
			return 'https://cdn.example.com/image.webp';
		} );

		$attachment = $this->create_image( [ 'file' => 'test-200px.jpg' ] );
		$image      = \Timmy\Timmy::get_image( $attachment, [
			'resize' => [ 200 ],
			'webp'   => true,
		] );

		$this->assertEquals( 'https://cdn.example.com/image.webp', $image->src() );
		$this->assertEquals( 'https://cdn.example.com/image.webp', $image->src( [ 'webp' => false ] ) );
		$this->assertFileDoesNotExist( $this->get_upload_path() . '/test-200px.webp' );
	}

	/**
	 * Makes sure that the original image is used without resizing when the filter doesn’t return
	 * a URL and the original image already has the requested width.
	 */
	public function test_image_url_for_original_width_falls_back_to_original() {
		$called = 0;

		$this->add_filter_temporarily( 'timmy/image/url', function( $url ) use ( &$called ) {
			$called++;

			return $url;
		} );

		$attachment = $this->create_image( [ 'file' => 'test-200px.jpg' ] );
		$image      = \Timmy\Timmy::get_image( $attachment, [ 'resize' => [ 200 ] ] );

		$this->assertEquals( $this->get_upload_url() . '/test-200px.jpg', $image->src() );
		$this->assertEquals( 1, $called );
	}

	public function test_image_url_for_full_and_original_size() {
		$received = [];

		$this->add_filter_temporarily( 'timmy/image/url', function( $url, $width, $height, $webp, $image ) use ( &$received ) {
			$received[] = [ $width, $height, $webp, $image->is_full_size() ];

			return 'https://cdn.example.com/image.jpg';
		}, 10, 5 );

		$attachment = $this->create_image();

		foreach ( [ 'full', 'original' ] as $size ) {
			$image = \Timmy\Timmy::get_image( $attachment, $size );

			$this->assertEquals( 'https://cdn.example.com/image.jpg', $image->src(), $size );
			$this->assertStringContainsString(
				'src="https://cdn.example.com/image.jpg"',
				get_timber_image_responsive( $attachment, $size ),
				$size
			);
		}

		$max_width  = \Timmy\Timmy::get_image( $attachment, 'full' )->max_width();
		$max_height = \Timmy\Timmy::get_image( $attachment, 'full' )->max_height();

		$this->assertNotEmpty( $received );

		foreach ( $received as $arguments ) {
			$this->assertEquals( [ $max_width, $max_height, false, true ], $arguments );
		}
	}

	public function test_image_url_for_full_size_falls_back_to_attachment_url() {
		$this->add_filter_temporarily( 'timmy/image/url', '__return_null' );

		$attachment = $this->create_image();

		$this->assertEquals(
			wp_get_attachment_url( $attachment->ID ),
			\Timmy\Timmy::get_image( $attachment, 'full' )->src()
		);
	}

	/**
	 * Makes sure that values other than a URL let Timmy build the URL itself.
	 */
	public function test_image_url_false_falls_back_to_resizing() {
		$this->add_filter_temporarily( 'timmy/image/url', '__return_false' );

		$attachment = $this->create_image();

		$this->assertEquals(
			$this->get_upload_url() . '/test-1400x0-c-default.jpg',
			get_timber_image_src( $attachment, 'large' )
		);
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
