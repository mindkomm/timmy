# Image Configuration

<!-- TOC depthTo:2 -->

- [Using an image size array instead of a key](#using-an-image-size-array-instead-of-a-key)
- [Image keys](#image-keys)
- [Image configuration options](#image-configuration-options)
- [Image configuration example](#image-configuration-example)

<!-- /TOC -->

Your image configuration is an array with all the image sizes. You name each image size via an array key that you will reference in [Timmy’s functions](./functions.md).

```php
add_filter( 'timmy/sizes', function( $sizes ) {
    return array(
        'thumbnail' => array(
            'resize' => array( 150, 150 ),
            'name' => 'Thumbnail',
            'post_types' => array( 'all' ),
        ),
        'col-4' => array(
            'resize' => array( 370 ),
            'srcset' => array( 2 ),
            'sizes' => '(min-width: 992px) 33.333vw, 100vw',
            'name' => 'Width 1/4',
            'post_types' => array( 'post', 'page' ),
        ),
    );
} );
```

## Using an image size array instead of a key

Instead of having to define each size you want to use in the image configuration, you can also pass in a configuration array to a function directly. This is helpful if an image size appears only in one place or if you want to use something other than the image configuration array. Here’s an example for [`get_timber_image_responsive`](#get_timber_image_responsive):

```php
<img<?php echo get_timber_image_responsive( get_post_thumbnail_id(), array(
    'resize' => array( 570 ),
    'srcset' => array( 0.5, 2 ),
    'sizes'  => '(min-width: 62rem) 50vw, 100vw',
    'name'   => 'Width 1/2',
) ); ?>>
```

In Twig, you’ll have to use the hash notation:

```twig
<img{{ post.thumbnail|get_timber_image_responsive({
    resize: [570],
    srcset: [0.5, 2],
    sizes: '(min-width: 62rem) 50vw, 100vw',
    name: 'Width 1/2'
}) }}>
```

## Image keys

Choose your image keys so you can identify or remember them best.

```php
'gallery-thumbnail' => array( /* Image size options come here */ ),
```

However, consider this when choosing keys:

### The `thumbnail` key

Use a `thumbnail` key in your configuration. This image size will be used to show thumbnails in the backend. Remember when you set all image sizes to 0? We deactivated thumbnails there. If we didn’t use the `thumbnail` key, WordPress would show the original size of the images in a small thumbnail. This would lead to long page load times, because a lot of image data has to be downloaded when you visit Media administration in the backend.

When you use a `thumbnail` key, Timmy will tell WordPress to use that size for thumbnails in the backend. Otherwise it will use **the first size that you define in the array**. Because of this, you probably want to start with smaller images in your configuration array and go up to the biggest.

### The `large` key

For better compatibility with third party plugins like Yoast SEO, it’s best to always define a `large` key. Yoast will take this size to generate the markup for OG image tags.

### The `full` and `original` keys

You shouldn’t use `full` or `original` as keys in your configuration. If you define these, you will get errors when you upload images.

* `full` – This size is reserved for the full (and maybe scaled) size of an image. ([Scaled image versions](https://make.wordpress.org/core/2019/10/09/introducing-handling-of-big-images-in-wordpress-5-3/) for big images were introduced in WordPress 5.3)
* `original` – This size is reserved for the original size of the image (the image as it was uploaded to WordPress).

## Image configuration options

- [resize](#resize)
- [srcset](#srcset)
- [sizes](#sizes)
- [letterbox](#letterbox)
- [tojpg](#tojpg)
- [post_types](#post_types)
- [name](#name)
- [show_in_ui](#show_in_ui)
- [generate_srcset_sizes](#generate_srcset_sizes)
- [oversize](#oversize)

---

### resize

(`array`), required

This is the normal size at which the image is displayed.

For each images size, you need to define a `resize` key that contains the parameters later given to the resize function (more about this on <https://timber.github.io/docs/guides/cookbook-images/#arbitrary-resizing-of-images>).

```php
'resize' => array( 370, 270 ),
```

If you do not set a second value in the array, the image will not be cropped.

```php
'resize' => array( 370 ),
```

You can use a third param, which is the crop settings.

```php
'resize' => array( 370, 270, 'center' ),
```

> In cropping it will crop starting from the top edge. The other cropping options are: 'default' (which generally crops from the center, but in vertical situations has a bias toward preserving the top of the image), 'center', 'top', 'bottom', 'left' and 'right'. – from <https://timber.github.io/docs/reference/timber-image/>

---

### srcset

(`array`), optional, Default: `array( array() )`

These are alternative sizes when you want to use responsive images. Read more about this on <http://scottjehl.github.io/picturefill/>.

For high-density screen support, you can add a bigger size than the standard image, provided the original uploaded image is at least that size. To save bandwidth, it doesn’t necessarily have to be the doubled size. Maybe a resize of 1.5 will suffice.

```php
'srcset' => array(
    array( 768, 329 ),
    array( 480, 206 ),
),
```

If you want to, you can also use a **ratio number** of the size you want to use on the additional src. It will automatically scale the width and the height based on what is set in the 'resize' array.

```php
'srcset' => array(
    0.3,
    0.5,
    2, // For a resize of ( 1400, 600 ), this is the same as array( 2800, 1200 )
),
```

The sizes added in the srcset option will automatically be added to the srcset output together with the image size in resize in ascending order.

Additionally to using integers to define the ratio an image should be resized with, you can also use strings with an `x` descriptor.

```php
'srcset' => array( '1x', '2x' ),
```

This will update the resulting srcset so you get `1x` and `2x` descriptors instead of `1400w` and `2800w`.

```html
<img srcset="header_example-1400x600-c-default.jpg 1x,
    header_example-2800x1200-c-default.jpg 2x">
```

---

### sizes

(`string`), optional, Default: `''`

This is the string for the sizes attribute for the picture polyfill. Read more about this on <http://scottjehl.github.io/picturefill/>.

```php
/**
 * «For all screen widths above 62rem the image will be displayed at 33.333vw
 * (33% of the viewport width), otherwise it will use 100vw (100% of the
 * viewport width).»
 */
'sizes' => '(min-width: 62rem) 33.333vw, 100vw',
```

```php
/**
 * «For all screen widths up until 61.9375rem, the image will displayed at a
 * width of 125vm, for screen widths above at a width of 100vw.»
 */
'sizes' => '(max-width: 61.9375rem) 125vw, 100vw',
```

#### Source order matters!

Picturefill will know which image size to use, **if you use the right order**.

* The first media condition that matches will be used.
* If you use `max-width`, arrange them from the smallest to the largest values.
* If you use `min-width`, arrange the from the largest to the smallest value.

---

### letterbox

(`bool`|`string`), optional, Default: `#000000`

Letterbox the image to the size given in [`resize`](#resize) with a default black background (`#000000`). [Letterboxing](https://en.wikipedia.org/wiki/Letterboxing_(filming)) contains an image to a certain size without cropping, but with filling the extra space with a color.

Letterboxing only works if both **width and height are not** `0`.

```php
'letterbox' => true,
```

You can also use another hex color value for the letterbox color:

```php
'letterbox' => '#bada55',
```

---

### tojpg

(`bool`|`string`), optional, Default: `#ffffff`

Converts an image to JPG if the source is a PNG image and uses the assigned color to fill the transparent space in the PNG image. The value `true` uses the default white background color (`#ffffff`) to fill transparent space.

```php
'tojpg' => true,
```

You can also use another hex color value:

```php
'tojpg' => '#c0ffee',
```

Assigning a color to fill the transparent space is not possible with the [normal `tojpg` Timber filter](https://timber.github.io/docs/guides/cookbook-images/#converting-images).

---

### post_types

(`array`), optional, Default: `array( '', 'post', page )`

When you want to restrict generating of image sizes to certain post types while uploading an image in the backend, you can define a `post_types` key containing an array with all the post types you want to allow. If you omit that key, post types `post` and `page` as well as attachments not assigned to any post (defined with `''`) will be used as defaults.

Say you want an image size to only be used for pages and an *employee* post type:

```php
'post_types' => array( 'page', 'employee' ),
```

If you use that image size with another post type, it will still be resized on the fly when needed.

You can use `array( 'all' )` to always generate the size for all post types.

```php
'post_types' => array( 'all' ),
```

You can use an empty array for `post_type` to ignore the size entirely when an image is uploaded. The image size will still be generated on the fly when needed.

```php
'post_types' => array(),
```

---

### name

(`string`), optional, Default: `''`

The name parameter is used in the backend. When `show_in_ui` is `true`, then this name will be shown to the user, when she selects an image to be inserted into the editor. It’s just for ease of use.

---

### show_in_ui

(`bool`), optional, Default: `true`

When you set this to false, the user will not be able to select that value in the backend, e.g. when she wants to insert a Media file directly into the WYSYWIG content.

If the post type a user is editing is not in the `post_types` array (and if `post_types` is not `all`, the size will not be shown to the user.

---

### generate_srcset_sizes

(`bool`), optional, Default: `false`

Per default, all the sizes defined under `srcset` will only be generated when the image is requested in the frontend. Only the size defined in `resize` will be generated. By setting this to true, srcset sizes will also be generated when an image is uploaded.

You can use the [`timmy/generate_srcset_sizes` filter](#timmy-generate-srcset-sizes) to enable or disable this globally. Setting this option on an image size always takes precedence over the filter.

---

### oversize

(`bool|array`), optional, Default: `array( 'allow' => false, 'style_attr' => true )`

Controls whether the image should be resized to a bigger size than its original size.

When `allow` is `true`, Timmy will check the size of the original image to see if it’s big enough to be resized with the given parameters. If not, Timmy returns the image at the original size, but still considers additional image sizes smaller than the original size to be added to srcset.

If you want to disable this and allow images to grow bigger than the original size, set the value of `allow` to `true`:

```php
// Allow srcset sizes bigger than the original size of the image
'oversize' => [
    'allow' => true,
],
```

You can also use the [`timmy/oversize` filter](#timmyoversize) to control this behavior for all image sizes.

#### Inline style attributes

Timmy adds inline style attributes to the image to set the width or height in px. This prevents the image to be displayed bigger than its size. This is useful if you autosize the image to the size of its container with CSS (`max-width: 100%; height: auto;`).

If you want to disable inline style attributes, set `style_attr` to `false`.

```php
/**
 * Only output srcset sizes smaller or equal the original size of the image,
 * but do not add style attributes.
 */ 
'oversize' => array(
    'allow' => false,
    'style_attr' => false,
),
```

If `allow` is set to `true`, inline styles will never be applied.

#### Shortcuts

You can use a boolean for `oversize` to set both `allow` and `style_attr` values at the same time:

```php
'oversize' => false,
```

This is a shortcut for: 

```php
// Allow srcset sizes bigger than the original size of the image
'oversize' => array(
    'allow'      => false,
    'style_attr' => false,
),
```

## Image configuration example

You will add this to `functions.php` of your theme:

```php
add_filter( 'timmy/sizes', function( $sizes ) {
    return array(
        /**
         * The thumbnail size is used to show thumbnails in the backend.
         * You should always have an entry with the 'thumbnail' key.
         */
        'thumbnail'     => array(
            'resize'     => array( 150, 150 ),
            'name'       => 'Thumbnail',
            'post_types' => array( 'all' ),
        ),
        'custom-4'      => array(
            'resize' => array( 370 ),
            'sizes'  => '(min-width: 62rem) 33.333vw, 100vw',
            'name'   => 'Width 1/4',
        ),
        'custom-4-crop' => array(
            'resize'     => array( 370, 270 ),
            'srcset'     => array( 2 ),
            'sizes'      => '(min-width: 62rem) 33.333vw, 100vw',
            'name'       => 'Width 1/4 fix',
            'show_in_ui' => false,
            'post_types' => array( 'example', 'post', 'page' ),
        ),
        'custom-6'      => array(
            // If you do not set a second value in the array, the image will not be cropped
            'resize'     => array( 570 ),
            'srcset'     => array( 0.5, 2 ),
            'sizes'      => '(min-width: 62rem) 50vw, 100vw',
            'name'       => 'Width 1/2',
            'post_types' => array( 'example' ),
        ),
        'sponsor-logo'  => array(
            'resize'    => array( 370, 370 ),
            /**
             * Letterbox the image with white color,
             * using dimensions 370x370 from "resize" key
             */
            'letterbox' => '#ffffff',
            // And also convert it to JPG if it is a PNG
            'tojpg'     => true,
        ),
        'large'         => array(
            'resize'     => array( 1400 ),
            'show_in_ui' => false,
        ),
        // 14:6 crop
        'header'        => array(
            // This is the normal size at which the image is displayed
            'resize'                => array( 1400, 600 ),
            // These are alternative sizes for responsiveness
            'srcset'                => array(
                array( 768, 329 ),
                array( 480, 206 ),
                2, // This is the same as array(2800, 1200)
            ),
            'sizes'                 => '(max-width: 61.9375rem) 125vw, 100vw',
            'show_in_ui'            => false,
            'generate_srcset_sizes' => true,
        ),
    );
} );
```
