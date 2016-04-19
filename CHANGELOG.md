# Changelog

## 0.10.0 - 

- **Changed image configuration option `size` to `sizes`** to use the attribute name that is also used in the HTML markup. This means you have to update your image configuration.
- Integrated Timmy’s image sizes better into default WordPress functionalities to better support other plugins like Advanced Custom Fields, Admin Columns by codepress and Yoast SEO.
- Improved README with section about image size keys. See README.
- Fixed call to deprecated method.
- Fixed error when `0` was used for the width parameter.

## 0.9.3 - 2016-03-17

- Add support for resize values of `0` for the width parameter. In Timber, a user can pass the value 0 as the width parameter. Images will then be resized proportionally based on the height parameter. This now also works in Timmy. (Thanks to @lmartins for pointing this out.)

## 0.9.2 - 2016-03-06
- Improved examples in README.
- Added support for letterbox and tojpg filters. See README for more information.
- Made sure other plugins are loaded before plugin is initialized
