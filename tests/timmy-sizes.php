<?php

// Reset thumbs sizes
set_post_thumbnail_size( 0, 0 );
add_image_size( 'thumbnail', 0, 0 );
add_image_size( 'medium', 0, 0 );
add_image_size( 'medium_large', 0, 0 );
add_image_size( 'large', 0, 0 );

/**
 * Image sizes for testing Timmy.
 *
 * @return array Array containing all image sizes used on a page.
 */
add_filter( 'timmy/sizes', function( $sizes ) {
	return array_merge( $sizes, [
		'thumbnail'                           => [
			'resize'     => [ 150, 150 ],
			'name'       => 'Thumbnail',
			'post_types' => [ 'all' ],
			'sizes'      => '100vw',
		],
		'resize-only'                         => [
			'resize' => [ 500 ],
		],
		'medium'                              => [
			'resize' => [ 600 ],
		],
		'large'                               => [
			'resize' => [ 1400 ],
			'srcset' => [ [ 560 ] ],
			'sizes'  => '100vw',
		],
		'large-x-descriptors'                 => [
			'resize' => [ 1400 ],
			'srcset' => [ [ 560 ], '1x', '1.5x' ],
			'sizes'  => '100vw',
		],
		'webp'                                => [
			'resize' => [ 1400 ],
			'srcset' => [ [ 560 ] ],
			'sizes'  => '100vw',
			'webp' => true,
		],
		'webp-quality-100'                    => [
			'resize' => [ 1400 ],
			'webp' => [
				'quality' => 100,
			],
		],
		'picture'                             => [
			'type'   => 'picture',
			'resize' => [ 1400 ],
			'srcset' => [ [ 560 ] ],
			'sizes'  => '100vw',
		],
		'picture-webp'                        => [
			'type'   => 'picture',
			'resize' => [ 1400 ],
			'srcset' => [ [ 560 ] ],
			'sizes'  => '100vw',
			'webp' => true,
		],
		'picture-webp-with-small-image'       => [
			'resize'     => [ 1400 ],
			'srcset'     => [ [ 560 ] ],
			'show_in_ui' => false,
			'webp'     => true,
		],
		'header-wide'                         => [
			'resize'                => [ 1400 ],
			'srcset'                => [
				[ 768, 329 ],
				1.5,
			],
			'sizes'                 => '100vw',
			'name'                  => 'Full width',
			'generate_srcset_sizes' => true,
			'show_in_ui'            => false,
			'post_types'            => [ 'page' ],
		],
		'responsive-content-image'            => [
			'resize' => [ 400 ],
			'srcset' => [ [ 370 ], [ 768 ] ],
			'sizes'  => '100vw',
		],
		'one-size'                            => [
			'resize' => [ 768 ],
			'sizes'  => '50vw',
		],
		'upscale-default'                     => [
			'resize' => [ 1401 ],
			'srcset' => [ [ 150 ] ],
		],
		'upscale-default-portrait'            => [
			'resize' => [ 0, 1402 ],
			'srcset' => [ [ 0, 150 ] ],
		],
		'upscale-allow-true'                  => [
			'resize'  => [ 1403 ],
			'srcset'  => [ [ 150 ] ],
			'upscale' => [
				'allow' => true,
			],
		],
		'deprecated-oversize'                 => [
			'resize'   => [ 1404 ],
			'srcset'   => [ [ 150 ] ],
			'oversize' => [
				'allow' => true,
			],
		],
		'upscale-allow-true-style-attr-false' => [
			'resize'  => [ 1405 ],
			'srcset'  => [ [ 150 ] ],
			'upscale' => [
				'allow'      => true,
				'style_attr' => false,
			],
		],
	] );
} );
