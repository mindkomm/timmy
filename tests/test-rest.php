<?php

use Timmy\Helper;

class TestRest extends TimmyUnitTestCase {
	function test_show_in_rest()
    {
		add_post_type_support( 'page', 'thumbnail' );

        $post_id = self::factory()->post->create([
			'post_type' => 'page',
        ]);
		$attachment_id = $this->create_image_attachment( $post_id );

		// Simulate REST context
	    if ( ! defined( 'REST_REQUEST' ) ) {
	        define( 'REST_REQUEST', true );
	    }

		// Create REST request
        $request = new WP_REST_Request( 'GET', rest_get_route_for_post_type_items('page') );
		$request->set_param( '_embed', 'wp:featuredmedia' );
		$request->set_param( 'per_page', 1 );
		$response = rest_do_request( $request );
		// Internal requests need to be handled differently.
	    // @link https://developer.wordpress.org/rest-api/frequently-asked-questions/#how-do-i-use-the-_embed-parameter-on-internal-requests
        $data = rest_get_server()->response_to_data( $response, true );

	    $this->assertSame( 200, $response->get_status() );

		$page = $data[0];
		$sizes = $page['_embedded']['wp:featuredmedia'][0]['media_details']['sizes'];
		$this->assertArrayNotHasKey('resize-only', $sizes);

		$timmy_sizes = Helper::get_image_sizes();

		// Same size because of the 'full' size that is present in generated sizes but no Timmy’s sizes.
		$this->assertCount( count( $timmy_sizes ), $sizes );
    }
}
