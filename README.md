# Timmy

Timmy is an opt-in library/plugin for [Timber](http://upstatement.com/timber/) to make it more convenient to work with responsive images.

In your Twig template, you can do this:

```twig
<img{{ post.thumbnail|get_timber_image_responsive('custom-6') }}>
```

To get this:

```html
<img srcset="https://www.mind.ch/wp-content/uploads/2016/02/header_example-480x206-c-default.jpg 480w,
    https://www.mind.ch/wp-content/uploads/2016/02/header_example-768x329-c-default.jpg 768w,
    https://www.mind.ch/wp-content/uploads/2016/02/header_example-1400x600-c-default.jpg 1400w,
    https://www.mind.ch/wp-content/uploads/2016/02/header_example-2800x1200-c-default.jpg 2800w"
sizes="100vw"
alt="Your alt text"
title="Your image title">
```

You can use Timmy with non-Timber WordPress themes.

---

### Contents

- [Features](#features)
- [Getting Started/Preparations](#getting-startedpreparations)
- [Functions](#functions)
- [Image Configuration](#image-configuration)
- [Image Configuration Example](#image-configuration-example)
- [Filters](#filters)
- [Responsive Content Images](#responsive-content-images)
- [Best Practices](#best-practices)
- [FAQ](#faq)

---

## Features

Timber already comes with a set of really nice features for handling images. Especially the [**arbitrary resizing of images**](https://timber.github.io/docs/guides/cookbook-images/#arbitrary-resizing-of-images) is very convenient. Whenever a page is accessed and the image size can’t be found, it will be created on the fly. You can use as many different image sizes as you like, without always having to use plugins like [Regenerate Thumbnails](https://wordpress.org/plugins/regenerate-thumbnails/) when you make a change to the default WordPress image sizes.

**Timmy** uses Timber’s `TimberImageHelper` class to enhance this functionality even more:

### Mimicks default WordPress image functionalities in some ways

* **You can have as many defined image sizes as you want**. It’s easier to work with named image sizes like `thumbnail`, `medium`, `portrait` etc. Timmy lets you define each image size with a lot of [handy configuration options](#image-configuration).

* **Users can select different image sizes in WYSYWIG editor**. Normally, a user can only select the default WordPress sizes *Thumbnail*, *Medium*, *Large* and *Full*. With images defined through Timmy, a user [can select all image sizes that you define](https://cloud.githubusercontent.com/assets/2084481/13374936/bfb58ec2-dd92-11e5-9e05-cc22fe4f0f88.png), without the default sizes.

* **Makes images inserted into a post’s content via WordPress Editor responsive**.

* **Integration for popular plugins** like [Advanced Custom Fields](https://www.advancedcustomfields.com/), [Admin Colums](https://www.admincolumns.com/) and [Yoast SEO](https://yoast.com/wordpress/plugins/seo/). Because Timmy tells WordPress that there are image sizes present, other plugins will allow you to select images defined through Timmy, like the preview images for image fields defined with ACF or a preview image used in Admin Columns.

* **You can still use Regenerate Thumbnails**. Using [Regenerate Thumbnails](https://wordpress.org/plugins/regenerate-thumbnails/) with Timmy will clean your uploads folder from image sizes you don’t need anymore. If you have no image sizes defined with Timmy, Timmy will just delete all image sizes generated with TimberImageHelper. But no worries, remember that Timber automatically creates an image size if it doesn’t already exist. If you have existing image sizes from the default WordPress resize functionality that weren’t resized with Timber, you can use [Force Regenerate Thumbnails](https://wordpress.org/plugins/force-regenerate-thumbnails/) to also delete all of those.

* **You can still use Timber’s resize functions**. Timber has some [really neat image manipulation functions](https://timber.github.io/docs/guides/cookbook-images/). You can still use these or you can also use a mix of the two.

### Helps you with image HTML output

* **Responsive images**. For each image size, you can define additional sizes that will be used for the responsive image srcset. You can use this together with [Picturefill](https://scottjehl.github.io/picturefill/) (Timmy doesn’t come with Picturefill itself. You will have to include it yourself).

* **Accessibility**. Timmy automatically pulls image descriptions and alternative texts for `alt` and `title` tags.

### Reasonable image generation

* **Image sizes are generated when they are uploaded**. When you use Timber images you don’t have to care about image sizes being present in the uploads folder. If your frontend is accessed, Timber creates image sizes when they don’t already exist. You’d always have to visit the frontend to make sure the first visitor of a page doesn’t have really long loading times. Because Timmy knows which sizes you want to use – you defined them – it will generate them for you. There are cases where this is useful, e.g. when some posts are created automatically and you also pull in images.

* **Restrict to post types to prevent bloat**. If you want to use an image size just for one post type, you can define that. This will prevent bloating up your uploads folder with image sizes that are never used on the site.

### Limitations
* We don’t know if Timmy works with images hosted on Content Delivery Networks (CDN). We haven’t looked into that yet and we don’t know if we ever will. Pull requests welcome ;).

## Getting Started/Preparations

In order to make Timmy work, you’ll have to

### 1. Install Timber and Timmy

You can either install both Timmy and Timber as plugins or use Composer.

#### Install as Plugin

1. Install [Timber Library Plugin](https://wordpress.org/plugins/timber-library/). You don’t have to necessarily go full Timber with your theme. You can use Timber and Timmy to only handle your images in your theme.

2. Then [download and install the latest version of Timmy](<https://github.com/MINDKomm/Timmy/releases/latest>). (Timmy currently can’t be found in the official WordPress plugin directory. Maybe it will be soon.)

#### Install with Composer

```
composer require mindkomm/timmy
```

* The benefit of installing Timmy through Composer is that you add it as a dependency of your theme, which puts you in full control of the version you want to work with.
* Timmy requires Timber, so you won’t have to necessarily install Timber separately.

Require the autoload file at the top of your **functions.php**:

```php
require_once( __DIR__ . '/vendor/autoload.php' );
```

Now initialize Timmy right after Timber:

```php
new Timber\Timber();
new Timmy\Timmy();
```

### 2. Prepare Media Settings

Set all image sizes in WordPress media settings to `0`.

![](https://cloud.githubusercontent.com/assets/2084481/24502221/2ce43aa0-154d-11e7-8cc0-b64abc65891a.png)

If you use WPCLI, you can run the following commands:

```sh
wp option update thumbnail_size_w 0
wp option update thumbnail_size_h 0
wp option update medium_size_w 0
wp option update medium_size_h 0
wp option update medium_large_size_h 0
wp option update medium_large_size_w 0
wp option update large_size_w 0
wp option update large_size_h 0
```

### 3. Reset post thumbnail size

In `functions.php`, make sure `set_post_thumbnail_size()` is set to `0, 0`

```php
set_post_thumbnail_size( 0, 0 );
```

### 4. Register your image sizes with Timmy

Add a filter for `timmy/sizes` in `functions.php` of your theme or in a file that is required or included from `functions.php`. This filter should return an array with your [Image Configuration](#image-configuration).

#### Example

```php
add_filter( 'timmy/sizes', function( $sizes ) {
    return array(
        'custom-4' => array(
            'resize' => array( 370 ),
            'srcset' => array( 2 ),
            'sizes' => '(min-width: 992px) 33.333vw, 100vw',
            'name' => 'Width 1/4 fix',
            'post_types' => array( 'post', 'page' ),
        ),
    );
} );
```

The array key (`custom-4` in the example above) will be used to reference the image when you want to load it in your template.

### 5. Performance optimizations

If you use Advanced Custom Fields, check the [Best Practices](#best-practices) section for how to load images faster.

## Functions

You can use the following functions to get your images into your template:

### Basic stuff
* [get_timber_image()](#get_timber_image) – Returns the src attribute together with optional alt and title attributes for an image.
* [get_timber_image_src()](#get_timber_image_src) – Returns the src for an image.

### Responsive Images
* [get_timber_image_responsive()](#get_timber_image_responsive) – Returns the srcset, size, alt and title attributes for an image.
* [get_timber_image_responsive_src()](#get_timber_image_responsive_src) – Returns the srcset and sizes for an image. This is practically the same as *get_timber_image_responsive*, just without alt and title tags.
* [get_timber_image_responsive_acf()](#get_timber_image_responsive_acf) – Takes the field name of an ACF image as the input and returns the same output as *get_timber_image_responsive()*.

### Additional Helpers
* [get_post_thumbnail()](#get_post_thumbnail) – Returns the src, alt and title attributes for a post thumbnail at a given size.
* [get_post_thumbnail_src()](#get_post_thumbnail_src) – Returns the src for a post thumbnail. This is practically the same as *get_post_thumbnail*, just without alt and title tags.

---

### get_timber_image

`get_timber_image( int $post_id|TimberImage $timberImage, string|array $size )`

Returns the src attribute together with optional alt and title attributes for a TimberImage.

##### Usage in WordPress templates

```php
<img<?php echo get_timber_image( get_post_thumbnail_id(), 'custom-4' ); ?>>
```

##### Usage in Twig

For Twig, this function is used as a filter on the TimberImage appended with a `|`.

```twig
<img{{ post.thumbnail|get_timber_image('custom-4-crop') }}>
```

---

### get_timber_image_src

`get_timber_image_src( int $post_id|TimberImage $timber_image, string|array $size )`

Returns the src for a TimberImage.

##### Usage in WordPress templates

```php
<img src="<?php echo get_timber_image_src( get_post_thumbnail_id(), 'custom-4-crop' ); ?>">
```

##### Usage in Twig

```twig
<img{{ post.thumbnail|get_timber_image_src('custom-4-crop') }}>
```

---

### get_timber_image_responsive

`get_timber_image_responsive( int $post_id|TimberImage $timber_image, string|array $size )`

Returns the srcset, size, alt and title attributes for a TimberImage. If this function is used with a SVG image, the single src will be returned instead of srcset.

##### Usage in WordPress templates

```php
<img<?php echo get_timber_image_responsive( get_post_thumbnail_id(), 'custom-6' ); ?>>
```

##### Usage in Twig

```twig
<img{{ post.thumbnail|get_timber_image_responsive('custom-6') }}>
```

---

### get_timber_image_responsive_src

Returns the srcset and sizes for a TimberImage. This is practically the same as `get_timber_image_responsive`, just without alt and title tags.

---

### get_timber_image_responsive_acf

`get_timber_image_responsive_acf( string $field_name, string|array $size )`

Takes the field name of an ACF image as the input and returns the same output as `get_timber_image_responsive()`.

##### Usage in WordPress templates

```php
<img<?php echo get_timber_image_responsive_acf( 'image', 'custom-4-crop' ); ?>>
<!-- Will use get_field( 'image' ) to get the image information -->
```

##### Usage in Twig

You won’t use this function as a filter like the ones above.

```twig
<img{{ get_timber_image_responsive_acf('image', 'custom-4-crop') }}>
```

---

### get_post_thumbnail

`get_post_thumbnail( int $post_id, string|array $size = 'post-thumbnail' )`

Returns the src, alt and title attributes for a post thumbnail at a given size.

##### Usage in WordPress templates

```php
<img<?php echo get_post_thumbnail( get_post_thumbnail_id(), 'custom-6' ); ?>>
```

##### Usage in Twig

In Twig combined with Timber you will already have the post thumbnail through `post.thumbnail`. No need to wrap it in another function.

---

### get_post_thumbnail_src

`get_post_thumbnail_src( int $post_id, string|array $size = 'post-thumbnail' )`

Returns the src for a post thumbnail. This is practically the same as `get_post_thumbnail`, just without alt and title tags.

---

## Image Configuration

Your image configuration is an array with all the image sizes. You name each image size via array key that you will reference in the functions above.

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

### Using an image size array instead of a key

Instead of having to define each size you want to use in the image configuration, you can also pass in an array with your configuration to a function directly. This is helpful if an image size appears only in one place or if you want to use something other than the image configuration array. Here’s an example for [`get_timber_image_responsive`](#get_timber_image_responsive):

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

## Image configuration options

* [resize](#resize)
* [srcset](#srcset)
* [sizes](#sizes)
* [letterbox](#letterbox)
* [tojpg](#tojpg)
* [post_types](#post_types)
* [name](#name)
* [show_in_ui](#show_in_ui)
* [generate_srcset_sizes](#generate_srcset_sizes)
* [oversize](#oversize)

### Image Keys (the image size identifier)

Choose your image keys so you can identify or remember them best.

```php
'gallery-thumbnail' => array( /* Image size options come here */ ),
```

However, consider this when choosing keys:

#### The `thumbnail` key

Use a `thumbnail` key in your configuration. This image size will be used to show thumbnails in the backend. Remember when you set all image sizes to `0`? We deactivated thumbnails there. WordPress would now show the original size of the images in a small thumbnail. This leads to long page load times and a lot of traffic when you visit Media administration in the backend.

When you use a `thumbnail` key, Timmy will tell WordPress to use that size for thumbnails in the backend. Otherwise it will use **the first size that you define in the array**. Because of this, you probably want to start with smaller images and go up to the biggest.

#### Reserved keys

You shoudn’t use the following keys in your configuration

* `full`
* `original`

These sizes are reserved for the original size of the image. If you define these, you will get errors when you upload images.

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

When you want to restrict generating of image sizes to certain post types while uploading an image in the backend, you can define a `post_types` key containing an array with all the post types you want to allow. If you omit that key, post types `post` and `page` as well as attachments not assigned to any post will be used as defaults.

Say you want an image size only to be used for pages and an *employee* post type:

```php
'post_types' => array( 'page', 'employee' ),
```

If you use that image size with another post type, it will be resized on the fly.

You can use `post_types' => array( 'all' )` to always generate this size, for all post types.

```php
'post_types' => array( 'all' ),
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

Allows the image to be resized to a bigger size than its original size.

Timmy checks the size of the original image to see if it’s big enough to be resized with the given parameters. If not, Timmy returns the image at the original size, but still considers additional image sizes smaller than the original size to be added to srcset.

If you want to disable this and allow images grow bigger than the original size, set the value to `true`:

```php
'oversize' => true,
```

This is a shortcut for: 

```php
// Allow srcset sizes bigger than the original size of the image
'oversize' => array(
    'allow' => true,
),
```

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

---

## Image configuration example

You will add this to `functions.php` of your theme:

```php
add_filter( 'timmy/sizes', function( $sizes ) {
    return array(
        /**
         * The thumbnail size is used to show thumbnails in the backend.
         * You should always have an entry with the 'thumbnail' key.
         */
        'thumbnail' => array(
            'resize' => array( 150, 150 ),
            'name' => 'Thumbnail',
            'post_types' => array( 'all' ),
        ),
        'custom-4' => array(
            'resize' => array( 370 ),
            'sizes' => '(min-width: 62rem) 33.333vw, 100vw',
            'name' => 'Width 1/4',
        ),
        'custom-4-crop' => array(
            'resize' => array( 370, 270 ),
            'srcset' => array( 2 ),
            'sizes' => '(min-width: 62rem) 33.333vw, 100vw',
            'name' => 'Width 1/4 fix',
            'show_in_ui' => false,
            'post_types' => array( 'example', 'post', 'page' ),
        ),
        'custom-6' => array(
            // If you do not set a second value in the array, the image will not be cropped
            'resize' => array( 570 ),
            'srcset' => array( 0.5, 2 ),
            'sizes' => '(min-width: 62rem) 50vw, 100vw',
            'name' => 'Width 1/2',
            'post_types' => array( 'example' ),
        ),
        'sponsor-logo' => array(
            'resize' => array( 370, 370 ),
            /**
             * Letterbox the image with white color,
             * using dimensions 370x370 from "resize" key
             */
            'letterbox' => '#ffffff',
            // And also convert it to JPG if it is a PNG
            'tojpg' => true,
        )
        // 14:6 crop
        'header' => array(
            // This is the normal size at which the image is displayed
            'resize' => array( 1400, 600 ),
            // These are alternative sizes for responsiveness
            'srcset' => array(
                array( 768, 329 ),
                array( 480, 206 ),
                2, // This is the same as array(2800, 1200)
            ),
            'sizes' => '(max-width: 61.9375rem) 125vw, 100vw',
            'show_in_ui' => false,
            'resize_srcset' => true,
        ),
    );
} );
```

## Filters

### timmy/sizes

Filters the image sizes used in Timmy. Read more about this in [Image Configuration](#image-configuration).

**Parameters**

- **$sizes**  
	*(array)* Image configuration array. Default `array()`.

---

### timmy/resize/ignore

Filters whether we should resize an image size.

When true is returned in this filter, the function will bailout early and the image will not be processed further.

**Parameters**

- **$ignore**  
	*(bool)* Whether to ignore an image size. Default `false`.
- **$attachment**  
	*(WP_Post)* The attachment post.
- **$size**  
	*(string)* The requested image size.
- **$file_src**  
	*(string)* The file src URL.

**Example**

The following filter is already included in Timmy by default. 

```php
add_filter( 'timmy/resize/ignore', function() {
    // Ignore GIF images
    if ( 'image/gif' === $attachment->post_mime_type ) {
    	return true;
    }
    
    return $return;
} );
```

---

### timmy/generate_srcset_sizes

Filters whether srcset sizes should be generated when an image is uploaded.

**Parameters**

- **$generate_srcset_sizes**  
    *(bool)* Whether to generate srcset sizes. Passing false will prevent srcset sizes to generated when an image is uploaded. Default `false`.
- **$key**  
	*(string)* The image size key.
- **$img_size**  
    *(array)* The image size configuration array.
- **$attachment**  
    *(WP_Post)* The attachment post.

**Example**

```php
// Generate srcset sizes for all image sizes
add_filter( 'timmy/generate_srcset_sizes', '__return_true' );
```

## Responsive Content Images

Timmy can make images added to the WYSIWYG editor responsive.

- Image sizes are selectable in the backend by default. If you don’t want an image size to show up in the backend, use `show_in_ui => false`.
- Selects the size defined in your image configuration to build up the responsive markup through [`get_timber_image_responsive()`](#get_timber_image_responsive).

**functions.php**

Enable the functionality in your theme by instantiating the feature in your **functions.php**:

```php
new Timmy\Responsive_Content_Images(); 
```

## Best Practices

### Working with Advanced Custom Fields

The functions provided by Timmy accept ACF image arrays, but they only take the value of `ID` to convert it to a TimberImage. To save performance and to prevent ACF from looping through all your defined image sizes, it’s better to **only return the image ID** in the ACF field group settings instead of the whole image array.

![](https://cloud.githubusercontent.com/assets/2084481/26151756/6fd5bf78-3b04-11e7-86ac-d7523f47684b.png)

You can also do this programmatically for all fields with type `image`:

```php
add_filter( 'acf/load_field/type=image', function( $field ) {
    $field['return_format'] = 'id';

    return $field;
} );
```

## FAQ

### Why is my image not displaying?

Timmy silently fails (returns `false`) when an image can’t be found. You might want to check with `{{ dump(yourimage) }}` if you really try to use one of Timmy’s functions on your image.

### How does Timmy handle SVG images?

Timmy will always try to return the image src for an SVG image, without any responsive markup (even if you call it with a function that normally returns responsive markup, like `get_timber_image_responsive()`).

### How does Timmy handle GIF images?

Timber can resize GIF images when you have Imagick installed. So GIF images behave the same way than normal images. However, resizing GIF images takes quite some time. That’s why Timmy tries to reduce the amount of sizes that have to be generated.

When a GIF is uploaded to the backend, it will only be resized to the `thumbnail` size defined in your image configuration. GIF images in the Media Library will be loaded in a smaller size, because this makes the Media Grid load faster. All other image size are ignored when uploading an image. GIF images are still resized on the fly.

### How can I better control the markup for SVG and GIF images?

If you want more control over the markup for SVG or GIF images, you can catch them through the mime type:

```twig
{# Assuming that image is an instance of Timber\Image #}
{% if image.post_mime_type == 'image/svg+xml' %}
    <img class="img-svg" src="{{ image.src }}" alt="">
{% elseif image.post_mime_type == 'image/gif'}
    <img class="img-gif" src="{{ image.src }}" alt="">
{% else %}
    {# Default for images #}
    <img{{ image|get_timber_image_responsive('your-size') }}>
{% endif %}
```

### How can I make the full size unavailable when an image is inserted into the WordPress Editor?

Timmy uses the filter `image_size_names_choose` with a standard priority of 10 to return the image sizes configured with Timmy and additionally adds the full size of an image. Add the following filter to your theme functions to remove the full size again. 

```php
add_filter( 'image_size_names_choose', function( $sizes ) {
    unset( $sizes['full'] );
    return $sizes;
}, 11 );
```

### How does it work again with the srcset and sizes parameters?

The `sizes` and `srcset` attribute tells the browser which images sizes are available and let’s **the browser choose which image size to display** based on the current viewport size, caching settings etc.

This image by [Harry Roberts](https://twitter.com/csswizardry/status/836960832789565440) shows a simple way to explain/remember what it means:

![](https://cloud.githubusercontent.com/assets/2084481/24998864/d938d100-203b-11e7-8233-3b0a48b81c13.jpg)

## Why is my backend unresponsive?

With Timmy’s logic, when an image size is changed in the configuration, it can trigger an on-the-fly regeneration of an image. This can lead to performance problems, because resizing an image takes quite some time. Timmy is optimized to be very performant in the backend and tries to keep resizes to the minimum.

In the following case it will always trigger a resize:

- Changing the image size of the `thumbnail` size.

When you do this, or if you see your Media takes a very long time loading images, or doesn’t load them at all, it might be a good idea to run Regenerate Thumbnails.

## Future Plans

* Make `letterbox` option work properly in combination with `oversize` option.
