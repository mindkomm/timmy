# Changelog

## 0.13.6 - 2018-04-11

- Escape HTML attributes with `esc_attr()` for better security and to prevent errors when certain characters are used in image alt texts and titles.

## 0.13.5 - 2018-03-09

- Changed how the bug that was fixed in 0.13.4 is handled. Solve it differently, so that the changes made for the `timmy/resize/ignore` filter in 0.13.4 could be reversed. Check the [filter section in the README](https://github.com/mindkomm/timmy#filters) for how the `timmy/resize/ignore` filter works. Sorry!
- Fixed a whitespace bug in `get_timber_image_responsive_src()` that was introduced in 0.13.3 (a17aa2e825c0b0b20e613f70d7aa735711170367).

## 0.13.4 - 2018-03-06

- Fixed a bug when `timmy/resize/ignore` filter was ignored in the backend when attachments were queried.
- <del>Changed parameters passed to `timmy/resize/ignore` filter. Instead of the `$attachment_id` parameter, there’s now a `$mime_type` parameter. The order for the following parameters changed as well. Check the [filter section in the README](https://github.com/mindkomm/timmy#filters).</del>

## 0.13.3 - 2018-02-26

- Added lazy loading helper [filter](https://github.com/mindkomm/timmy#lazy-loading) and [function](https://github.com/mindkomm/timmy#make_timber_image_lazy).

## 0.13.2 - 2018-02-14

- Fixed a bug when Timmy tried to convert a PDF to JPEG. Timmy now checks that a file is not a PDF before converting it to JPG.

## 0.13.1 - 2018-02-08

- Fixed behavior of `oversize` parameter. Prior to this version, images would grow bigger than their original size even if `oversize['allow']` was set to `false`.
- Extended the `oversize` shortcut to also set the `style_attr` parameter. This means that you can use `oversize => true` or `oversize => false` to set all parameters directly. This change only has implications for you if you’ve used `oversize => false` in the past. 
- Added new filter `timmy\oversize` to set the default values for the oversize parameter. Read more about it in the [Filters section of the README](https://github.com/mindkomm/timmy#filters).

## 0.13.0 - 2018-01-18

### Composer type

Changed Composer package type from `wordpress-plugin` to `library`.

With type `wordpress-plugin`, the package was installed into a `wp-content/plugins` folder, even if you used it in your theme. With type `library`, we follow that Timber does, and it’s still possible to [define where the package should be installed](https://getcomposer.org/doc/faqs/how-do-i-install-a-package-to-a-custom-path-for-my-framework.md). And it will not break backwards compatibility, because the default folder will still be the `vendors` folder.

### Introducing filters

In the upcoming versions of Timmy you’ll see filters that allow you to change certain settings more easily. In this version, we introduce 3 new filters. To read more about the filters, there’s a new [Filters section of the README](https://github.com/mindkomm/timmy#filters). Here’s an overview over what these changes mean for you.

#### timmy/sizes

Introduced a new `timmy/sizes` filter to define the image sizes used in Timmy. The way to get image sizes through `get_image_sizes()` will be deprecated in a future version of Timmy. The reason for is that `get_image_sizes()` is quite a generic function name that could lead to conflicts with other plugins.

This means that instead of returning your image configuration from `get_image_sizes()`, you’ll use `timmy/sizes`:

**DON’T USE**

```php
function get_image_sizes() {
    return array(
        // Image configuration
    );
}
```

**USE**

```php
add_filter( 'timmy/sizes', function() {
    return array(
        // Image configuration
    );
} );
```

#### timmy/resize/ignore

- Introduced a `timmy/resize/ignore` filter to make it possible to ignore resizing of images. This filter allows you to disable the resizing based on various conditions, that you can define yourself based on the values passed to this filter.
- Added a default filter to ignore resizing of GIF images by default. If you still want to enable resizing of GIF images, you can disable the filter by adding the following line to `functions.php` of your theme (after you initialized Timmy with `new Timmy\Timmy()`):

```php
remove_filter( 'timmy/resize/ignore', array( 'Timmy\Timmy', 'ignore_gif' ) );
```

#### timmy/generate_srcset_sizes

Introduced a new filter `timmy/generate_srcset_sizes` that filters whether srcset sizes should be generated when an image is uploaded. By default, Timmy generated sizes defined in `srcset` in your image configuration when you uploaded an image in the backend by default. You could disable this by using a `generate_srcset_sizes` in your image configuration. This behavior is now changed:

- `generate_srcset_sizes` will be `false` by default. If you want a size to generate srcset sizes when an image is uploaded, you need to set `generate_srcset_sizes` for an image size to `true`.
- If you want to enable generating srcset sizes for all images sizes, you can use the `timmy/generate_srcset_sizes` filter.
- A value for `generate_srcset_sizes` set on an image size in your configuration will always overwrite values set by the filter.

## 0.12.2 - 2018-01-09

- Added type `wordpress-plugin` to `composer.json`. This allows you to install Timmy in a folder other than `vendor/mindkomm/timmy` (#11, Thanks, @salaros). [Read more about installing a package to a custom path](https://getcomposer.org/doc/faqs/how-do-i-install-a-package-to-a-custom-path-for-my-framework.md).
- Added possibility to [use an image size array directly for frontend functions](https://github.com/mindkomm/timmy#using-an-image-size-array-instead-of-a-key), instead of adding it to the global image configuration array.

## 0.12.1 - 2017-08-17

Changed how Timmy is initialized. It now works the same as initializing Timber. You need to initialize it manually in **functions.php** of your theme:

```php
new Timmy\Timmy();
```  

You can add this right after you called `new Timber\Timber();`.

This change was required to make the library more compatible with environments like Bedrock, where WordPress might not have been loaded when the Composer package is initialized.

**Other changes**

- Fixed missing files when Timmy is installed as a plugin.
- Fixed leading whitespace for `srcset` attributes.

## 0.12.0 - 2017-08-03

- Added support for [responsive content images](https://github.com/MINDKomm/timmy#responsive-content-images), which means that Timmy can now make images inserted in the post content via the WordPress Editor responsive.
- Optimized image markup by using the `srcset` attribute only if multiple images are available. If an image has only one image in srcset, it falls back to using the `src` attribute instead.
- Added `src` fallback attribute to all responsive images by default to fix invalid markup errors (as [recommended by Picturefill](http://scottjehl.github.io/picturefill/#support)). 
- Optimized performance in the backend.
	- Only thumbnails and full sizes of images are shown in the backend to prevent on-the-fly resizing of images (e.g. in the Media Grid).
	- Uses full size instead of large size if 'large' is present as a size.
- Fixed selectable images sizes that appear in various positions in the backend.
- Added fallback for GIFs to return full size when image metadata can’t be read.
- Internal: Introduced new helper class for static helper functions.
- Internal: Replaced deprecated filter `get_twig` with `timber/twig`.

## 0.11.0 - 2017-04-26

- Made Timmy compatible with newest version 1.3.0 of Timber, which is now also the minimum required version.
- Added warning when key "full" is used in image size config.
- Added warning when an image size does not exist in the image configuration.
- Improved how Timmy selects the correct image source internally.
- Improved how SVG and GIF images are handled. See the [FAQ section](https://github.com/MINDKomm/timmy#how-does-timmy-handle-svg-images) for more information.
- Improved how Timmy handles images it can’t find. Now, it will return `false` for all images it can’t find. This means that it will silently fail without any error messages.

## 0.10.6 - 2017-02-21

- Fixed compatibility issue with Timber 1.2.2, where Timber returned the thumbnail size of an image instead of the full size.

## 0.10.5 - 2017-01-03

- Fixed notice that occurred when oversize parameter was not set in image config.

## 0.10.4 - 2017-01-02

- Fixed `oversize` parameter and updated documentation.

## 0.10.3 - 2016-11-18

- Fixed an error when Timmy blocked images showing up in the Media Grid view in the backend.

## 0.10.2 - 2016-10-11

- Optimized function `get_timber_image_responsive_src()` to directly return the image source when the image is an SVG or a GIF.

## 0.10.1 - 2016-05-25

- Added composer.json to make it possible to load Timmy through Composer.
- Added check for valid and non-empty TimberImage. Frontend functions now return an empty string when no image was found.
- Made sure image arrays (like used in ACF) are also converted to a TimberImage. This way, it doesn’t matter if an image ID or an array is returned by ACF, Timmy will convert it to a TimberImage.

## 0.10.0 - 2016-05-09

- **Changed image configuration option `size` to `sizes`** to use the attribute name that is also used in the HTML markup. This means you have to update your image configuration.
- Added functionality that prevent smaller images from being oversized. In the image configuration, there’s a new option: `oversize`. False per default, it can be set to true to allow images to grow bigger than their uploaded size. Otherwise, Timmy returns only sizes smaller or equal than the uploaded image size and also adds a style attribute "width" or "height", to prevent the image to autogrow in the browser.
- Integrated Timmy’s image sizes better into default WordPress functionalities to better support other plugins like Advanced Custom Fields, Admin Columns by codepress and Yoast SEO.
- Improved README with section about image size keys. See README.
- Fixed call to deprecated method.
- Fixed error when `0` was used for the width parameter.
- Namespaced all instances of calls to Timber classes. Timber classes are namespaced since Timber v1.0.0.

## 0.9.3 - 2016-03-17

- Added support for resize values of `0` for the width parameter. In Timber, a user can pass the value 0 as the width parameter. Images will then be resized proportionally based on the height parameter. This now also works in Timmy. (Thanks to @koraysels for pointing this out.)

## 0.9.2 - 2016-03-06
- Improved examples in README.
- Added support for letterbox and tojpg filters. See README for more information.
- Made sure other plugins are loaded before plugin is initialized
