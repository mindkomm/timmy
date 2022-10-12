<?php

use Timmy\Responsive_Content_Images;
use Timmy\Timmy;

/**
 * Class TestResponsiveContentImages
 */
class TestResponsiveContentImages extends TimmyUnitTestCase {
	public function test_classic_image() {
		$image = $this->create_image();
		$rci   = Timmy::responsive_content_images();

		$content = sprintf(
			'<p><img class="alignnone size-responsive-content-image wp-image-%2$s" src="%1$s/test-400x0-c-default.jpg" alt="" width="400" /></p>',
			$this->get_upload_url(),
			$image->ID
		);

		$expected = sprintf(
			'<p><img srcset="%1$s/test-370x0-c-default.jpg 370w, %1$s/test-400x0-c-default.jpg 400w, %1$s/test-768x0-c-default.jpg 768w" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" sizes="100vw" width="400" height="267" loading="lazy" alt="" class="alignnone size-responsive-content-image wp-image-%2$s"></p>',
			$this->get_upload_url(),
			$image->ID
		);
		$result = $rci->make_content_images_responsive( $content );

		$this->assertEquals( $expected, $result );
	}

	public function test_block_image() {
		$image = $this->create_image();
		$rci   = Timmy::responsive_content_images();

		$content = trim( do_blocks( sprintf( '<!-- wp:image {"id":40,"sizeSlug":"responsive-content-image"} -->
<figure class="wp-block-image size-responsive-content-image"><img src="%1$s/dog-400x0-c-default.jpg" alt="" class="wp-image-%2$s"/></figure>
<!-- /wp:image -->',
			$this->get_upload_url(),
			$image->ID
		) ) );

		$expected = sprintf(
			'<figure class="wp-block-image size-responsive-content-image"><img srcset="%1$s/test-370x0-c-default.jpg 370w, %1$s/test-400x0-c-default.jpg 400w, %1$s/test-768x0-c-default.jpg 768w" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" sizes="100vw" width="400" height="267" loading="lazy" alt="" class="wp-image-%2$s"></figure>',
			$this->get_upload_url(),
			$image->ID
		);
		$result = $rci->make_content_images_responsive( $content );

		$this->assertEquals( $expected, $result );
	}

	public function test_combined_classic_and_block_images() {
		$image = $this->create_image();
		$rci   = Timmy::responsive_content_images();

		$content = sprintf(
			'<p><img class="alignnone size-responsive-content-image wp-image-%2$s" src="%1$s/test-400x0-c-default.jpg" alt="" width="400" /></p>

<!-- wp:image {"id":40,"sizeSlug":"responsive-content-image"} -->
<figure class="wp-block-image size-responsive-content-image"><img src="%1$s/dog-400x0-c-default.jpg" alt="" class="wp-image-%2$s"/></figure>
<!-- /wp:image -->',
			$this->get_upload_url(),
			$image->ID
		);

		$content = trim( do_blocks( $content ) );

		$expected = sprintf(
			'<p><img srcset="%1$s/test-370x0-c-default.jpg 370w, %1$s/test-400x0-c-default.jpg 400w, %1$s/test-768x0-c-default.jpg 768w" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" sizes="100vw" width="400" height="267" loading="lazy" alt="" class="alignnone size-responsive-content-image wp-image-%2$s"></p>


<figure class="wp-block-image size-responsive-content-image"><img srcset="%1$s/test-370x0-c-default.jpg 370w, %1$s/test-400x0-c-default.jpg 400w, %1$s/test-768x0-c-default.jpg 768w" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" sizes="100vw" width="400" height="267" loading="lazy" alt="" class="wp-image-%2$s"></figure>',
			$this->get_upload_url(),
			$image->ID
		);
		$result = $rci->make_content_images_responsive( $content );

		$this->assertEquals( $expected, $result );
	}

	function test_block_image_with_figcaption() {
		$image = $this->create_image();
		$rci   = Timmy::responsive_content_images();

		$content = sprintf( '<!-- wp:image {"id":40,"sizeSlug":"responsive-content-image"} -->
<figure class="wp-block-image size-responsive-content-image"><img src="%1$s/dog-400x0-c-default.jpg" alt="" class="wp-image-%2$s"/><figcaption>Image with a caption and a break<br>at 100 percent</figcaption></figure>
<!-- /wp:image -->',
			$this->get_upload_url(),
			$image->ID
		);

		$content = trim( do_blocks( $content ) );

		$expected = sprintf(
			'<figure class="wp-block-image size-responsive-content-image"><img srcset="%1$s/test-370x0-c-default.jpg 370w, %1$s/test-400x0-c-default.jpg 400w, %1$s/test-768x0-c-default.jpg 768w" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" sizes="100vw" width="400" height="267" loading="lazy" alt="" class="wp-image-%2$s"><figcaption>Image with a caption and a break<br>at 100 percent</figcaption></figure>',
			$this->get_upload_url(),
			$image->ID
		);
		$result = $rci->make_content_images_responsive( $content );

		$this->assertEquals( $expected, $result );
	}

	function test_block_image_resized_at_75_percent() {
		$image = $this->create_image();
		$rci   = Timmy::responsive_content_images();

		$content = sprintf( '<!-- wp:image {"id":40,"width":300,"height":200,"sizeSlug":"responsive-content-image"} -->
<figure class="wp-block-image size-responsive-content-image is-resized"><img src="%1$s/dog-400x0-c-default.jpg" alt="" class="wp-image-%2$s" width="300" height="200"/><figcaption>Image with a caption and a break<br>at 100%</figcaption></figure>
<!-- /wp:image -->',
			$this->get_upload_url(),
			$image->ID
		);

		$content = trim( do_blocks( $content ) );

		$expected = sprintf(
			'<figure class="wp-block-image size-responsive-content-image is-resized"><img width="300" height="200" srcset="%1$s/test-370x0-c-default.jpg 370w, %1$s/test-400x0-c-default.jpg 400w, %1$s/test-768x0-c-default.jpg 768w" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" sizes="100vw" loading="lazy" alt="" class="wp-image-%2$s"><figcaption>Image with a caption and a break<br>at 100/figcaption></figure>',
			$this->get_upload_url(),
			$image->ID
		);
		$result = $rci->make_content_images_responsive( $content );

		$this->assertEquals( $expected, $result );
	}

	function test_block_image_with_alt_text() {
		$image = $this->create_image();
		$rci   = Timmy::responsive_content_images();

		$content = sprintf( '<!-- wp:image {"id":40,"sizeSlug":"responsive-content-image"} -->
<figure class="wp-block-image size-responsive-content-image"><img src="%1$s/dog-400x0-c-default.jpg" alt="A dog wrapped in a blanket" class="wp-image-%2$s"/></figure>
<!-- /wp:image -->',
			$this->get_upload_url(),
			$image->ID
		);

		$content = trim( do_blocks( $content ) );

		$expected = sprintf(
			'<figure class="wp-block-image size-responsive-content-image"><img alt="A dog wrapped in a blanket" srcset="%1$s/test-370x0-c-default.jpg 370w, %1$s/test-400x0-c-default.jpg 400w, %1$s/test-768x0-c-default.jpg 768w" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" sizes="100vw" width="400" height="267" loading="lazy" class="wp-image-%2$s"></figure>',
			$this->get_upload_url(),
			$image->ID
		);
		$result = $rci->make_content_images_responsive( $content );

		$this->assertEquals( $expected, $result );
	}

	/**
	 * @link https://github.com/mindkomm/timmy/pull/37
	 */
	function test_block_image_pr_37() {
		$image = $this->create_image();
		$rci   = Timmy::responsive_content_images();

		$content = sprintf(
			'\n<figure class="wp-block-image size-large"><a href="https://foo"><img src="%1$s/dog-400x0-c-default.jpg" alt="" class="wp-image-%2$s"/></a></figure>\n',
			$this->get_upload_url(),
			$image->ID
		);

		$expected = sprintf(
			'\n<figure class="wp-block-image size-large"><a href="https://foo"><img srcset="%1$s/test-560x0-c-default.jpg 560w, %1$s/test-1400x0-c-default.jpg 1400w" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" sizes="100vw" width="1400" height="933" loading="lazy" alt="" class="wp-image-%2$s"></a></figure>\n',
			$this->get_upload_url(),
			$image->ID
		);

		$result = $rci->make_content_images_responsive( $content );

		$this->assertEquals( $expected, $result );
	}

	/**
	 * @link https://github.com/mindkomm/timmy/issues/40
	 */
	function test_block_image_with_other_classes() {
		$image = $this->create_image();
		$rci   = Timmy::responsive_content_images();

		$content = sprintf(
			'\n<figure class="wp-block-image alignfull size-large"><a href="https://example.org"><img src="%1$s/dog-400x0-c-default.jpg" alt="" class="wp-image-%2$s"/></a></figure>\n',
			$this->get_upload_url(),
			$image->ID
		);

		$expected = sprintf(
			'\n<figure class="wp-block-image alignfull size-large"><a href="https://example.org"><img srcset="%1$s/test-560x0-c-default.jpg 560w, %1$s/test-1400x0-c-default.jpg 1400w" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" sizes="100vw" width="1400" height="933" loading="lazy" alt="" class="wp-image-%2$s"></a></figure>\n',
			$this->get_upload_url(),
			$image->ID
		);

		$result = $rci->make_content_images_responsive( $content );

		$this->assertEquals( $expected, $result );
	}

	/**
	 * @link https://github.com/mindkomm/timmy/issues/40
	 */
	function test_block_image_with_even_more_classes() {
		$image = $this->create_image();
		$rci   = Timmy::responsive_content_images();

		$content = sprintf(
			'\n<figure class="wp-block-image alignfull is-style-special size-large and-another"><a href="https://example.org"><img src="%1$s/dog-400x0-c-default.jpg" alt="" class="wp-image-%2$s"/></a></figure>\n',
			$this->get_upload_url(),
			$image->ID
		);

		$expected = sprintf(
			'\n<figure class="wp-block-image alignfull is-style-special size-large and-another"><a href="https://example.org"><img srcset="%1$s/test-560x0-c-default.jpg 560w, %1$s/test-1400x0-c-default.jpg 1400w" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" sizes="100vw" width="1400" height="933" loading="lazy" alt="" class="wp-image-%2$s"></a></figure>\n',
			$this->get_upload_url(),
			$image->ID
		);

		$result = $rci->make_content_images_responsive( $content );

		$this->assertEquals( $expected, $result );
	}

	function test_block_image_with_rare_size_characters() {
		$filter = function( $sizes ) {
			$sizes = array_merge( $sizes, [
				'gallery/full_screen.and-other' => [
					'resize' => [ 1400 ],
					'srcset' => [ [ 560 ] ],
					'sizes'  => '100vw',
				],
			] );

			return $sizes;
		};

		$this->add_filter_temporarily( 'timmy/sizes/use_cache', '__return_false' );
		$this->add_filter_temporarily( 'timmy/sizes', $filter );

		$image = $this->create_image();
		$rci   = Timmy::responsive_content_images();

		$content = sprintf(
			'\n<figure class="wp-block-image alignfull size-gallery/full_screen.and-other"><a href="https://example.org"><img src="%1$s/dog-400x0-c-default.jpg" alt="" class="wp-image-%2$s"/></a></figure>\n',
			$this->get_upload_url(),
			$image->ID
		);

		$expected = sprintf(
			'\n<figure class="wp-block-image alignfull size-gallery/full_screen.and-other"><a href="https://example.org"><img srcset="%1$s/test-560x0-c-default.jpg 560w, %1$s/test-1400x0-c-default.jpg 1400w" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" sizes="100vw" width="1400" height="933" loading="lazy" alt="" class="wp-image-%2$s"></a></figure>\n',
			$this->get_upload_url(),
			$image->ID
		);

		$result = $rci->make_content_images_responsive( $content );

		$this->assertEquals( $expected, $result );
	}
}
