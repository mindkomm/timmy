# Installation

In order to make Timmy work, you’ll have to

- [1. Install Timmy](#1-install-timmy)
- [2. Prepare Media Settings](#2-prepare-media-settings)
- [3. Reset post thumbnail size](#3-reset-post-thumbnail-size)
- [4. Register your image sizes with Timmy](#4-register-your-image-sizes-with-timmy)
- [5. Use Picturefill](#5-use-picturefill)
- [6. Performance optimizations](#6-performance-optimizations)

## 1. Install Timmy

You can install Timmy with Composer

```
composer require mindkomm/timmy
```

Require the autoload file at the top of your **functions.php**:

```php
require_once __DIR__ . '/vendor/autoload.php';
```

Now initialize Timmy right after Timber:

```php
use Timber\Timber;
use Timmy\Timmy;

Timber::init();;
Timmy::init()
```

## 2. Prepare Media Settings

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

## 3. Reset post thumbnail size

In `functions.php`, make sure `set_post_thumbnail_size()` is set to `0, 0`

```php
set_post_thumbnail_size( 0, 0 );
```

## 4. Register your image sizes with Timmy

Add a filter for `timmy/sizes` in `functions.php` of your theme or in a file that is required or included from `functions.php`. This filter should return an array with your [Image Configuration](./image-configuration.md).

### Example

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

## 5. Use Picturefill

It’s recommended to use Timmy together with [Picturefill](https://scottjehl.github.io/picturefill/), to add support for responsive images to older browsers.

## 6. Performance optimizations

If you use Advanced Custom Fields, check the [Best Practices](./best-practices.md) section for how to load images faster.
