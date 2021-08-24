<?php

/**
 * Class TestUpscale
 */
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

		$this->assertFileNotExists( $file_path );
	}

	/**
	 * Tests whether upscaled image files are created when allowed.
	 */
	public function test_timmy_upscale_images_when_allowed() {
		add_filter( 'timmy/upscale', '__return_true' );

		$attachment = $this->create_image( [
			'file' => 'test-200px.jpg',
		] );

		image_downsize( $attachment->ID, 'large' );
		$file_path = $this->get_upload_path() . '/test-200px-1400x0-c-default.jpg';

		remove_filter( 'timmy/upscale', '__return_true' );

		$this->assertFileExists( $file_path );
	}

	/**
	 * Tests whether upscaled srcset sizes are not created.
	 */
	public function test_timmy_do_not_generate_srcset_sizes() {
		add_filter( 'timmy/generate_srcset_sizes', '__return_true' );

		$attachment = $this->create_image( [
			'file' => 'test-200px.jpg',
		] );

		image_downsize( $attachment->ID, 'large' );
		$file_path = $this->get_upload_path() . '/test-200px-560x0-c-default.jpg';

		remove_filter( 'timmy/generate_srcset_sizes', '__return_true' );

		$this->assertFileNotExists( $file_path );
	}

	/**
	 * Tests whether upscaled srcset image files are created when allowed.
	 */
	public function test_timmy_upscale_srcset_images_when_allowed() {
		add_filter( 'timmy/upscale', '__return_true' );
		add_filter( 'timmy/generate_srcset_sizes', '__return_true' );

		$attachment = $this->create_image( [
			'file' => 'test-200px.jpg',
		] );

		image_downsize( $attachment->ID, 'large' );
		$file_path = $this->get_upload_path() . '/test-200px-560x0-c-default.jpg';

		remove_filter( 'timmy/upscale', '__return_true' );
		remove_filter( 'timmy/generate_srcset_sizes', '__return_true' );

		$this->assertFileExists( $file_path );
	}
}
