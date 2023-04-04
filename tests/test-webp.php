<?php

use Timmy\Timmy;

class TestWebP extends TimmyUnitTestCase {
	public function test_webp() {
		$attachment = $this->create_image();

		$image     = wp_get_attachment_image_src( $attachment->ID, 'webp' );
		$file_path = $this->get_upload_path() . '/test-1400x0-c-default.webp';

		$this->assertEquals( $this->get_upload_url() . '/test-1400x0-c-default.webp', $image[0] );
		$this->assertFileExists( $file_path );
	}

	public function test_webp_quality() {
		$attachment = $this->create_image();

		$image     = wp_get_attachment_image_src( $attachment->ID, 'webp-quality-100' );
		$file_path = $this->get_upload_path() . '/test-1400x0-c-default.webp';

		$this->assertEquals( $this->get_upload_url() . '/test-1400x0-c-default.webp', $image[0] );
		$this->assertFileExists( $file_path );
	}

	public function test_picture_webp() {
		$alt_text   = 'Burrito Wrap';
		$attachment = $this->create_image( [
			'alt'         => $alt_text,
			'description' => 'Burritolino',
		] );
		$result     = get_timber_picture_responsive( $attachment, 'picture-webp' );

		$expected = sprintf(
			'<source type="image/webp" srcset="%1$s/test-560x0-c-default.webp 560w, %1$s/test-1400x0-c-default.webp 1400w" sizes="100vw">%2$s<source type="image/jpeg" srcset="%1$s/test-560x0-c-default.jpg 560w, %1$s/test-1400x0-c-default.jpg 1400w" sizes="100vw">%2$s<img src="%1$s/test-1400x0-c-default.jpg" width="1400" height="933" alt="Burrito Wrap" loading="lazy">',
			$this->get_upload_url(),
			PHP_EOL
		);

		$this->assertEquals( $expected, $result );
	}

	public function test_picture_webp_with_lazy_attributes() {
		$alt_text   = 'Burrito Wrap';
		$attachment = $this->create_image( [
			'alt'         => $alt_text,
			'description' => 'Burritolino',
		] );
		$result     = get_timber_picture_responsive( $attachment, 'picture-webp', [
			'lazy_srcset' => true,
			'lazy_src'    => true,
			'lazy_sizes'  => true,
		] );

		$expected = sprintf(
			'<source type="image/webp" data-srcset="%1$s/test-560x0-c-default.webp 560w, %1$s/test-1400x0-c-default.webp 1400w" data-sizes="100vw">%2$s<source type="image/jpeg" data-srcset="%1$s/test-560x0-c-default.jpg 560w, %1$s/test-1400x0-c-default.jpg 1400w" data-sizes="100vw">%2$s<img width="1400" height="933" alt="Burrito Wrap" loading="lazy" data-src="%1$s/test-1400x0-c-default.jpg">',
			$this->get_upload_url(),
			PHP_EOL
		);

		$this->assertEquals( $expected, $result );
	}

	public function test_picture_webp_with_small_image() {
		$attachment = $this->create_image( [
			'file' => 'test-200px.jpg',
		] );
		$result     = get_timber_picture_responsive( $attachment, 'picture-webp-with-small-image' );

		$expected = sprintf(
			'<source type="image/webp" srcset="%1$s/test-200px-200x0-c-default.webp">%2$s<source type="image/jpeg" srcset="%1$s/test-200px-200x0-c-default.jpg">%2$s<img src="%1$s/test-200px-200x0-c-default.jpg" width="200" height="133" alt="" loading="lazy">',
			$this->get_upload_url(),
			PHP_EOL
		);

		$this->assertEquals( $expected, $result );
	}

	public function test_picture_webp_with_small_image_square() {
		$attachment = $this->create_image( [
			'file' => 'test-200px.jpg',
		] );
		$result     = get_timber_picture_responsive( $attachment, 'picture-webp-resize-square' );

		$expected = sprintf(
			'<source type="image/webp" srcset="%1$s/test-200px-133x133-c-default.webp">%2$s<source type="image/jpeg" srcset="%1$s/test-200px-133x133-c-default.jpg">%2$s<img src="%1$s/test-200px-133x133-c-default.jpg" width="133" height="133" alt="" loading="lazy">',
			$this->get_upload_url(),
			PHP_EOL
		);

		$this->assertEquals( $expected, $result );
	}

	public function test_picture_with_full_src_webp() {
		$attachment = $this->create_image();

		$image = Timmy::get_image( $attachment, 'full' );
		$image->set_webp( true );

		$result = $image->picture_responsive();

		$expected = sprintf(
			'<source type="image/webp" srcset="%1$s/test.jpg">%2$s<img src="%1$s/test.jpg" width="2400" height="1600" alt="" loading="lazy">',
			$this->get_upload_url(),
			PHP_EOL
		);

		$this->assertEquals( $expected, $result );
	}

	public function test_picture_webp_args_array_with_srcset_descriptors() {
		$attachment = $this->create_image();
		$result     = get_timber_picture_responsive( $attachment, [
			'resize' => [ 260 ],
			'srcset' => [ '1x', '2x' ],
			'webp'   => true,
		] );

		$expected = sprintf(
			'<source type="image/webp" srcset="%1$s/test-260x0-c-default.webp 1x, %1$s/test-520x0-c-default.webp 2x">%2$s<source type="image/jpeg" srcset="%1$s/test-260x0-c-default.jpg 1x, %1$s/test-520x0-c-default.jpg 2x">%2$s<img src="%1$s/test-260x0-c-default.jpg" width="260" height="173" alt="" loading="lazy">',
			$this->get_upload_url(),
			PHP_EOL
		);

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

		$expected = sprintf(
			'<source type="image/webp" srcset="%1$s/test-260x0-c-default.webp 1x, %1$s/test-520x0-c-default.webp 2x">%2$s<source type="image/jpeg" srcset="%1$s/test-260x0-c-default.jpg 1x, %1$s/test-520x0-c-default.jpg 2x">%2$s<img src="%1$s/test-260x0-c-default.jpg" width="260" height="173" alt="" loading="lazy">',
			$this->get_upload_url(),
			PHP_EOL
		);

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
