<?php

use Timber\ImageHelper;

/**
 * Class TestAcf
 */
class TestAcf extends TimmyUnitTestCase {
	function set_up() {
		parent::set_up();

		require_once __DIR__ . '/assets/acf-image-field.php';
	}

	function test_get_timber_image_responsive_acf() {
		$post_id       = $this->factory->post->create();
		$attachment_id = $this->create_image_attachment( $post_id );

		$this->go_to( get_permalink( $post_id ) );

		update_field( 'image', $attachment_id, $post_id );

		$result  = get_timber_image_responsive_acf( 'image', 'large' );
		$result2 = get_timber_image_responsive( $attachment_id, 'large' );

		$this->assertEquals( $result, $result2 );
	}

	function test_get_timber_image_responsive_acf_in_twig() {
		$post_id       = $this->factory->post->create();
		$attachment_id = $this->create_image_attachment( $post_id );

		$this->go_to( get_permalink( $post_id ) );

		update_field( 'image', $attachment_id, $post_id );

		$twig = \Timber\Timber::compile_string(
			"<img {{ get_timber_image_responsive_acf('image', 'large') }}>"
		);

		$result = sprintf( '<img %s>', get_timber_image_responsive_acf( 'image', 'large' ) );

		$this->assertEquals( $result, $twig );
	}
}
