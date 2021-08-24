<?php

/**
 * Class TestUpscale
 */
class TestUpscale extends TimmyUnitTestCase {
	public function test_timmy_do_not_upscale_images() {
		$attachment = $this->create_image( [
			'file' => 'test-200px.jpg',
		] );

		image_downsize( $attachment->ID, 'large' );
		$file_path = $this->get_upload_path() . '/test-200px-1400x0-c-default.jpg';

		$this->assertFileNotExists( $file_path );
	}

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
}
