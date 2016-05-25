# Changelog

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
