<?php

/**
 * Class TestSvg
 */
class TestSvg extends TimmyUnitTestCase {
	/**
	 * Tests whether we get the full src of an SVG with size full.
	 *
	 * @since 0.14.4
	 */
	public function test_get_timber_image_full_with_svg() {
		$attachment = $this->create_image( [ 'file' => 'sveegee.svg' ] );
		$result     = get_timber_image( $attachment, 'full' );

		$image = ' src="' . $this->get_upload_url() . '/sveegee.svg" alt=""';

		$this->assertEquals( $image, $result );
	}

	/**
	 * Tests whether we get the full src of an SVG with size large.
	 *
	 * @since 0.14.4
	 */
	public function test_get_timber_image_large_with_svg() {
		$attachment = $this->create_image( [ 'file' => 'sveegee.svg' ] );
		$result     = get_timber_image( $attachment, 'large' );

		$image = ' src="' . $this->get_upload_url() . '/sveegee.svg" alt=""';

		$this->assertEquals( $image, $result );
	}

	public function test_svg_responsive_square() {
		$attachment = $this->create_image( [ 'file' => 'sveegee.svg' ] );
		$result     = get_timber_image_responsive( $attachment, 'large' );

		$image = ' src="' . $this->get_upload_url() . '/sveegee.svg" width="1400" height="1400" loading="lazy" alt=""';

		$this->assertEquals( $image, $result );
	}

	public function test_svg_responsive_rect() {
		$attachment = $this->create_image( [ 'file' => 'svg-400-200.svg' ] );
		$result     = get_timber_image_responsive( $attachment, 'large' );

		$image = ' src="' . $this->get_upload_url() . '/svg-400-200.svg" width="1400" height="700" loading="lazy" alt=""';

		$this->assertEquals( $image, $result );
	}

	public function test_svg_responsive_rect_without_viewbox() {
		$attachment = $this->create_image( [ 'file' => 'svg-without-viewbox.svg' ] );
		$result     = get_timber_image_responsive( $attachment, 'large' );

		$image = ' src="' . $this->get_upload_url() . '/svg-without-viewbox.svg" loading="lazy" alt=""';

		$this->assertEquals( $image, $result );
	}
}
