# Performance and Best Practices

<!-- TOC -->
- [Keep amount of generated images low](#keep-amount-of-generated-images-low)
- [Run Regenerate Thumbnails when you made changes to the image configuration](#run-regenerate-thumbnails-when-you-made-changes-to-the-image-configuration)
- [Working with Advanced Custom Fields](#working-with-advanced-custom-fields)
- [Working with WooCommerce](#working-with-woocommerce)
<!-- /TOC -->

## Keep amount of generated images low

With Timmy, it’s easy to define a lot of image sizes. This could make your site slow when uploading images, because a lot of sizes have to be generated. When you define image sizes, you can define the srcset sizes in a way where the dimensions overlap with your other image sizes.

Consider the following image configuration:

```php
add_filter( 'timmy/sizes', function( $sizes ) {
    return array(
        'thumbnail' => array(
            'resize' => array( 150, 150 ),
        ),
        'small' => array(
            'resize' => array( 370 ),
            'srcset' => array( array( 570 ) ),
        ),
        'small-crop' => array(
            'resize' => array( 370, 270 ),
        ),
        'large' => array(
            'resize' => array( 1400 ),
            'srcset' => array(
                array( 370 ),
                array( 570 ),
            )
        )
    );
} );
```

See how the image sizes use the same dimensions? By using a reduced set of dimensions throughout your configuration, you might get along with fewer image files.

## Run Regenerate Thumbnails when you made changes to the image configuration

You can make changes to your image configuration on an existing site. Timmy will deliver the updated image sizes and generate missing sizes on the fly. If you make a lot of changes, this can lead to **orphaned image files** that fill up the disk space on your server.

Timber will also generate image metadata and save it in the database when an image is resized. This metadata is sometimes used by third party plugins. For example, Yoast SEO uses this data to generate the markup for OG image tags. This **metadata is not updated** when new image sizes are generated. This might only be a problem if you **add or remove image sizes** or if you **change values in the `resize` option of an image size**.

To fix both problems, you can use [Regenerate Thumbnails](https://wordpress.org/plugins/regenerate-thumbnails/) plugin and let it run over all your images. This will delete orphaned image files and regenerate the meta data. If you don’t do this, your images will still show up, but compatibility is better if you do.

## Working with Advanced Custom Fields

The functions provided by Timmy accept ACF image arrays, but they only need the `ID` value to convert it to a TimberImage. To save performance and to prevent ACF from looping through all your defined image sizes, it’s better to **return the image ID** in the ACF field group settings instead of the whole image array.

![](https://cloud.githubusercontent.com/assets/2084481/26151756/6fd5bf78-3b04-11e7-86ac-d7523f47684b.png)

You can also do this programmatically for all fields with type `image`:

```php
/**
 * Always use 'id' as the return format for image fields.
 *
 * This will lead to a performance boost when using Timmy, which only needs the ID to work with.
 */
add_filter( 'acf/load_field/type=image', function( $field ) {
    $field['return_format'] = 'id';

    return $field;
} );
```

## Working with WooCommerce

When you run Timmy in combination with WooCommerce, you might define the image sizes that WooCommerce uses to let Timmy handle the images:

```php
add_filter( 'timmy/sizes', function( $sizes ) {
    return array(
        'woocommerce_thumbnail' => [
			'resize'     => [ 264, 176, 'center' ],
			'post_types' => [ 'product' ],
		],
		'woocommerce_single'    => [
			'resize'     => [ 412 ],
			'post_types' => [ 'product' ],
		],
    );
} );
```

However, if you do this, you might also want to disable the automatic image generation for these image sizes, because otherwise, WooCommerce will generate these image sizes on every page call where a WooCommerce image is requested, which will result in a massive performance drain.

```php
/**
 * Disable WooCommerce’s automatic image generation for certain WooCommerce sizes.
 */
add_filter( 'woocommerce_image_sizes_to_resize', function( $image_sizes ) {
    // Filter out images that shouldn’t be resized.
    $image_sizes = array_filter( $image_sizes, function( $image_size ) {
        return ! in_array( $image_size, [
            'woocommerce_thumbnail',
            'woocommerce_single',
        ], true );
    } );

    return $image_sizes;
} );
```

If you handle all WooCommerce images through Timmy, it might be easier to disable the automatic image generation for all images:

```php
add_filter( 'woocommerce_resize_images', '__return_false' );
```