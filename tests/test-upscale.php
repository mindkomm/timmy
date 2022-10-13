<?php

class TestUpscale extends TimmyUnitTestCase {
	/**
	 * Tests whether upscaled image file is not created.
	 */
	public function test_timmy_do_not_upscale_images() {
		$attachment = $this->create_image( [
			'file' => 'test-200px.jpg',
		] );

		image_downsize( $attachment->ID, 'large' );
		$file_path = $this->get_upload_path() . '/test-200px-1400x0-c-default.jpg';

		$this->assertFileDoesNotExist( $file_path );
	}

	/**
	 * Tests whether upscaled image files are created when allowed.
	 */
	public function test_timmy_upscale_images_when_allowed() {
		$this->add_filter_temporarily( 'timmy/upscale', '__return_true' );

		$attachment = $this->create_image( [
			'file' => 'test-200px.jpg',
		] );

		image_downsize( $attachment->ID, 'large' );
		$file_path = $this->get_upload_path() . '/test-200px-1400x0-c-default.jpg';

		$this->assertFileExists( $file_path );
	}

	/**
	 * Tests whether upscaled srcset sizes are not created.
	 */
	public function test_timmy_do_not_generate_srcset_sizes() {
		$this->add_filter_temporarily( 'timmy/generate_srcset_sizes', '__return_true' );

		$attachment = $this->create_image( [
			'file' => 'test-200px.jpg',
		] );

		image_downsize( $attachment->ID, 'large' );
		$file_path = $this->get_upload_path() . '/test-200px-560x0-c-default.jpg';

		$this->assertFileDoesNotExist( $file_path );
	}

	/**
	 * Tests whether upscaled srcset image files are created when allowed.
	 */
	public function test_timmy_upscale_srcset_images_when_allowed() {
		$this->add_filter_temporarily( 'timmy/upscale', '__return_true' );
		$this->add_filter_temporarily( 'timmy/generate_srcset_sizes', '__return_true' );

		$attachment = $this->create_image( [
			'file' => 'test-200px.jpg',
		] );

		image_downsize( $attachment->ID, 'large' );
		$file_path = $this->get_upload_path() . '/test-200px-560x0-c-default.jpg';

		$this->assertFileExists( $file_path );
	}

	public function test_upscale_default_allow_false() {
		$attachment = $this->create_image( [
			'file' => 'test-200px.jpg',
		] );
		$result     = get_timber_image_responsive( $attachment, 'upscale-default' );

		$expected = ' srcset="' . $this->get_upload_url() . '/test-200px-150x0-c-default.jpg 150w, ' . $this->get_upload_url() . '/test-200px-200x0-c-default.jpg 200w" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" width="200" height="133" loading="lazy" alt=""';

		$this->assertEquals( $expected, $result );
	}

	public function test_upscale_default_allow_false_portrait_image() {
		$attachment = $this->create_image( [
			'file' => 'test-133px-portrait.jpg',
		] );
		$result     = get_timber_image_responsive( $attachment, 'upscale-default-portrait' );

		$expected = ' srcset="' . $this->get_upload_url() . '/test-133px-portrait-0x150-c-default.jpg 100w, ' . $this->get_upload_url() . '/test-133px-portrait-133x200-c-default.jpg 133w" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" width="133" height="200" loading="lazy" alt=""';

		$this->assertEquals( $expected, $result );
	}

	public function test_upscale_allow_true() {
		$attachment = $this->create_image( [
			'file' => 'test-200px.jpg',
		] );
		$result = get_timber_image_responsive( $attachment, 'upscale-allow-true' );

		$expected = ' srcset="' . $this->get_upload_url() . '/test-200px-150x0-c-default.jpg 150w, ' . $this->get_upload_url() . '/test-200px-1403x0-c-default.jpg 1403w" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" width="1403" height="933" loading="lazy" alt=""';

		$this->assertEquals( $expected, $result );
	}

	public function test_upscale_allow_true_style_attr_false() {
		$attachment = $this->create_image( [
			'file' => 'test-200px.jpg',
		] );
		$result     = get_timber_image_responsive( $attachment, 'upscale-allow-true-style-attr-false' );

		$expected = ' srcset="' . $this->get_upload_url() . '/test-200px-150x0-c-default.jpg 150w, ' . $this->get_upload_url() . '/test-200px-1405x0-c-default.jpg 1405w" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" width="1405" height="934" loading="lazy" alt=""';

		$this->assertEquals( $expected, $result );
	}

	public function test_deprecated_oversize_parameter_naming() {
		$attachment = $this->create_image( [
			'file' => 'test-200px.jpg',
		] );
		$result = get_timber_image_responsive( $attachment, 'deprecated-oversize' );

		$expected = ' srcset="' . $this->get_upload_url() . '/test-200px-150x0-c-default.jpg 150w, ' . $this->get_upload_url() . '/test-200px-1404x0-c-default.jpg 1404w" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" width="1404" height="934" loading="lazy" alt=""';

		$this->assertEquals( $expected, $result );
	}

	public function test_wp_get_attachment_image_src_upscale() {
		$attachment = $this->create_image( [
			'file' => 'test-200px.jpg',
		] );
		$image      = wp_get_attachment_image_src( $attachment->ID, 'medium' );

		$url    = $image[0];
		$width  = $image[1];
		$height = $image[2];

		$expected_url = $this->get_upload_url() . '/test-200px.jpg';

		$this->assertEquals( $expected_url, $url );
		$this->assertEquals( 200, $width );
		$this->assertEquals( 133, $height );
	}

	public function test_wp_get_attachment_image_src_upscale_allow_true() {
		$attachment = $this->create_image( [
			'file' => 'test-200px.jpg',
		] );
		$image      = wp_get_attachment_image_src( $attachment->ID, 'upscale-allow-true' );

		$url    = $image[0];
		$width  = $image[1];
		$height = $image[2];

		$expected_url = $this->get_upload_url() . '/test-200px-1403x0-c-default.jpg';

		$this->assertEquals( $expected_url, $url );
		$this->assertEquals( 1403, $width );
		$this->assertEquals( 933, $height );
	}
}
