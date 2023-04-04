<?php

use Timmy\Timmy;

class TestColorSchemeDark extends TimmyUnitTestCase {
	public function test_picture() {
		$attachment = $this->create_image( [
			'alt'         => 'Burrito Wrap',
			'description' => 'Burritolino',
		] );

		$attachment_dark = $this->create_image( [
			'file' => 'test-color-scheme-dark.jpg',
		] );

		$image = Timmy::get_image( $attachment->ID, 'picture' );
		$image->set_color_scheme_dark_image( $attachment_dark->ID );

		$result = $image->picture_responsive();

		$expected = sprintf(
			'<source srcset="%1$s/test-color-scheme-dark-560x0-c-default.jpg 560w, %1$s/test-color-scheme-dark-1400x0-c-default.jpg 1400w" sizes="100vw" media="(prefers-color-scheme: dark)">%2$s<source srcset="%1$s/test-560x0-c-default.jpg 560w, %1$s/test-1400x0-c-default.jpg 1400w" sizes="100vw" media="(prefers-color-scheme: light)">%2$s<img src="%1$s/test-1400x0-c-default.jpg" width="1400" height="933" alt="Burrito Wrap" loading="lazy">',
			$this->get_upload_url(),
			PHP_EOL
		);

		$this->assertEquals( $expected, $result );
	}

	public function test_picture_loading_false() {
		$attachment      = $this->create_image();
		$attachment_dark = $this->create_image( [ 'file' => 'test-color-scheme-dark.jpg' ] );

		$image = Timmy::get_image( $attachment->ID, 'picture' );
		$image->set_color_scheme_dark_image( $attachment_dark->ID );

		$result = $image->picture_responsive( [ 'loading' => false ] );

		$expected = sprintf(
			'<source srcset="%1$s/test-color-scheme-dark-560x0-c-default.jpg 560w, %1$s/test-color-scheme-dark-1400x0-c-default.jpg 1400w" sizes="100vw" media="(prefers-color-scheme: dark)">%2$s<source srcset="%1$s/test-560x0-c-default.jpg 560w, %1$s/test-1400x0-c-default.jpg 1400w" sizes="100vw" media="(prefers-color-scheme: light)">%2$s<img src="%1$s/test-1400x0-c-default.jpg" width="1400" height="933" alt="">',
			$this->get_upload_url(),
			PHP_EOL
		);

		$this->assertEquals( $expected, $result );
	}

	public function test_picture_with_lazy_attributes() {
		$attachment = $this->create_image( [
			'alt'         => 'Burrito Wrap',
			'description' => 'Burritolino',
		] );

		$attachment_dark = $this->create_image( [
			'file' => 'test-color-scheme-dark.jpg',
		] );

		$image = Timmy::get_image( $attachment->ID, 'picture' );
		$image->set_color_scheme_dark_image( $attachment_dark->ID );

		$result = $image->picture_responsive( [
			'lazy_srcset' => true,
			'lazy_src'    => true,
			'lazy_sizes'  => true,
		] );

		$expected = sprintf(
			'<source data-srcset="%1$s/test-color-scheme-dark-560x0-c-default.jpg 560w, %1$s/test-color-scheme-dark-1400x0-c-default.jpg 1400w" data-sizes="100vw" media="(prefers-color-scheme: dark)">%2$s<source data-srcset="%1$s/test-560x0-c-default.jpg 560w, %1$s/test-1400x0-c-default.jpg 1400w" data-sizes="100vw" media="(prefers-color-scheme: light)">%2$s<img width="1400" height="933" alt="Burrito Wrap" loading="lazy" data-src="%1$s/test-1400x0-c-default.jpg">',
			$this->get_upload_url(),
			PHP_EOL
		);

		$this->assertEquals( $expected, $result );
	}

	public function test_picture_webp() {
		$attachment      = $this->create_image( [
			'alt'         => 'Burrito Wrap',
			'description' => 'Burritolino',
		] );
		$attachment_dark = $this->create_image( [
			'file' => 'test-color-scheme-dark.jpg',
		] );

		$image = Timmy::get_image( $attachment->ID, 'picture-webp' );
		$image->set_color_scheme_dark_image( $attachment_dark->ID );

		$result = $image->picture_responsive();

		$expected = sprintf(
			'<source type="image/webp" srcset="%1$s/test-color-scheme-dark-560x0-c-default.webp 560w, %1$s/test-color-scheme-dark-1400x0-c-default.webp 1400w" sizes="100vw" media="(prefers-color-scheme: dark)">%2$s<source type="image/jpeg" srcset="%1$s/test-color-scheme-dark-560x0-c-default.jpg 560w, %1$s/test-color-scheme-dark-1400x0-c-default.jpg 1400w" sizes="100vw" media="(prefers-color-scheme: dark)">%2$s<source type="image/webp" srcset="%1$s/test-560x0-c-default.webp 560w, %1$s/test-1400x0-c-default.webp 1400w" sizes="100vw" media="(prefers-color-scheme: light)">%2$s<source type="image/jpeg" srcset="%1$s/test-560x0-c-default.jpg 560w, %1$s/test-1400x0-c-default.jpg 1400w" sizes="100vw" media="(prefers-color-scheme: light)">%2$s<img src="%1$s/test-1400x0-c-default.jpg" width="1400" height="933" alt="Burrito Wrap" loading="lazy">',
			$this->get_upload_url(),
			PHP_EOL
		);

		$this->assertEquals( $expected, $result );
	}

	public function test_picture_webp_with_lazy_attributes() {
		$attachment      = $this->create_image( [
			'alt'         => 'Burrito Wrap',
			'description' => 'Burritolino',
		] );
		$attachment_dark = $this->create_image( [
			'file' => 'test-color-scheme-dark.jpg',
		] );

		$image = Timmy::get_image( $attachment->ID, 'picture-webp' );
		$image->set_color_scheme_dark_image( $attachment_dark->ID );

		$result = $image->picture_responsive( [
			'lazy_srcset' => true,
			'lazy_src'    => true,
			'lazy_sizes'  => true,
		] );

		$expected = sprintf(
			'<source type="image/webp" data-srcset="%1$s/test-color-scheme-dark-560x0-c-default.webp 560w, %1$s/test-color-scheme-dark-1400x0-c-default.webp 1400w" data-sizes="100vw" media="(prefers-color-scheme: dark)">%2$s<source type="image/jpeg" data-srcset="%1$s/test-color-scheme-dark-560x0-c-default.jpg 560w, %1$s/test-color-scheme-dark-1400x0-c-default.jpg 1400w" data-sizes="100vw" media="(prefers-color-scheme: dark)">%2$s<source type="image/webp" data-srcset="%1$s/test-560x0-c-default.webp 560w, %1$s/test-1400x0-c-default.webp 1400w" data-sizes="100vw" media="(prefers-color-scheme: light)">%2$s<source type="image/jpeg" data-srcset="%1$s/test-560x0-c-default.jpg 560w, %1$s/test-1400x0-c-default.jpg 1400w" data-sizes="100vw" media="(prefers-color-scheme: light)">%2$s<img width="1400" height="933" alt="Burrito Wrap" loading="lazy" data-src="%1$s/test-1400x0-c-default.jpg">',
			$this->get_upload_url(),
			PHP_EOL
		);

		$this->assertEquals( $expected, $result );
	}
}
