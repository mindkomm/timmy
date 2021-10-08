# Timmy

Timmy is an opt-in library/plugin to make it more convenient to work with responsive images. It was designed to be used with [Timber](http://upstatement.com/timber/), but should work with all your WordPress projects.

In your Twig template, you can do this:

```twig
<img{{ post.thumbnail|get_timber_image_responsive('custom-6') }}>
```

To get this:

```html
<img srcset="https://www.example.org/wp-content/uploads/2016/02/header_example-480x206-c-default.jpg 480w,
    https://www.example.org/wp-content/uploads/2016/02/header_example-768x329-c-default.jpg 768w,
    https://www.example.org/wp-content/uploads/2016/02/header_example-1400x600-c-default.jpg 1400w,
    https://www.example.org/wp-content/uploads/2016/02/header_example-2800x1200-c-default.jpg 2800w"
src="data:image/gif;base64,R0lGODlhAQABAAAAADs="
sizes="100vw"
alt="Your alt text"
>
```

## Documentation

- [Installation – Getting Started](./docs/installation.md)
- [Image Configuration](./docs/image-configuration.md)
- [Functions](./docs/functions.md)
- [Responsive Content Images](./docs/responsive-content-images.md)
- [Hooks (Filters)](./docs/hooks.md)
- [Lazy Loading](./docs/lazy-loading.md)
- [Performance and Best Practices](./docs/best-practices.md)
- [FAQ](./docs/faq.md)

## Features

Timber already comes with a set of really nice features for handling images. Especially the [arbitrary resizing of images](https://timber.github.io/docs/guides/cookbook-images/#arbitrary-resizing-of-images) is very convenient. Whenever a page is accessed and the image size can’t be found, it will be created on the fly. You can use as many different image sizes as you like, without always having to use plugins like [Regenerate Thumbnails](https://wordpress.org/plugins/regenerate-thumbnails/) when you make a change to the default WordPress image sizes.

**Timmy** uses Timber’s [`ImageHelper`](https://timber.github.io/docs/reference/timber-imagehelper/) class to enhance this functionality even more.

### Mimicks default WordPress image functionalities

* **You can have as many defined image sizes as you want**. It’s easier to work with named image sizes like `thumbnail`, `medium`, `portrait` etc. Timmy lets you define each image size with a lot of [handy configuration options](./docs/image-configuration.md).

* **Users can select different image sizes in WYSYWIG editor**. Normally, a user can only select the default WordPress sizes *Thumbnail*, *Medium*, *Large* and *Full*. With images defined through Timmy, a user [can select all image sizes that you define](https://cloud.githubusercontent.com/assets/2084481/13374936/bfb58ec2-dd92-11e5-9e05-cc22fe4f0f88.png), without the default sizes.

* **Makes images inserted into a post’s content via WordPress Editor responsive**.

* **Integration for popular plugins** like [Advanced Custom Fields](https://www.advancedcustomfields.com/), [Admin Columns](https://www.admincolumns.com/) and [Yoast SEO](https://yoast.com/wordpress/plugins/seo/). Because Timmy tells WordPress that there are image sizes present, other plugins will allow you to select images defined through Timmy, like the preview images for image fields defined with ACF or a preview image used in Admin Columns.

* **You can still use Regenerate Thumbnails**. Using [Regenerate Thumbnails](https://wordpress.org/plugins/regenerate-thumbnails/) with Timmy will clean your uploads folder from image sizes you don’t need anymore. If you have no image sizes defined with Timmy, Timmy will delete all image sizes generated with Timmy. But no worries, remember that Timber automatically creates an image size if it doesn’t already exist.

* **You can still use Timber’s resize functions**. Timber has some [really neat image manipulation functions](https://timber.github.io/docs/guides/cookbook-images/). You can still use these or you can also use a mix of the two.

### Helps you with image HTML output

* **Responsive images**. For each image size, you can define additional sizes that will be used for the responsive image srcset.
* **Lazy loading markup**. Adds lazy loading markup to your image.
* **Accessibility**. Timmy automatically pulls image alt texts and adds them to your image.

### Reasonable image generation

* **Image sizes are generated when they are uploaded**. When you use Timber images you don’t have to care about image sizes being present in the uploads folder. If your frontend is accessed, Timber creates image sizes when they don’t already exist. You’d always have to visit the frontend to make sure the first visitor of a page doesn’t have really long loading times. Because Timmy knows which sizes you want to use – you defined them – it will generate them for you. There are cases where this is useful, e.g. when some posts are created automatically and you also pull in images.

* **Restrict to post types to prevent bloat**. If you want to use an image size just for one post type, you can define that. This will prevent bloating up your uploads folder with image sizes that are never used on the site.

### Limitations

* We don’t know if Timmy works with images hosted on Content Delivery Networks (CDN). We haven’t looked into that yet and we don’t know if we ever will. Pull requests welcome ;).
