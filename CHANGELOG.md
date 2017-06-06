# Changelog

## 0.12.0 - 2017-06

- Added support for [responsive content images](https://github.com/MINDKomm/timmy#responsive-content-images), which means that Timmy can now make images inserted in the post content via the WordPress Editor responsive.
- Optimized image markup by using the `srcset` attribute only if multiple images are available. If an image has only one image in srcset, if falls back to using the `src` attribute instead.
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
