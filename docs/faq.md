# FAQ

- [Why is my image not displaying?](#why-is-my-image-not-displaying)
- [How does Timmy handle SVG images?](#how-does-timmy-handle-svg-images)
- [How does Timmy handle GIF images?](#how-does-timmy-handle-gif-images)
- [How can I better control the markup for SVG and GIF images?](#how-can-i-better-control-the-markup-for-svg-and-gif-images)
- [How can I make the full size unavailable when an image is inserted into the WordPress Editor?](#how-can-i-make-the-full-size-unavailable-when-an-image-is-inserted-into-the-wordpress-editor)
- [How does it work again with the srcset and sizes parameters?](#how-does-it-work-again-with-the-srcset-and-sizes-parameters)
- [Why is my backend unresponsive?](#why-is-my-backend-unresponsive)

## Why is my image not displaying?

Timmy silently fails (returns `false`) when an image can’t be found. You might want to check with `{{ dump(yourimage) }}` if you really try to use one of Timmy’s functions on your image.

## How does Timmy handle SVG images?

Timmy will always try to return the image src for an SVG image, without any responsive markup (even if you call it with a function that normally returns responsive markup, like `get_timber_image_responsive()`).

## How does Timmy handle GIF images?

Timber can resize GIF images when you have Imagick installed. So GIF images behave the same way than normal images. However, resizing GIF images takes quite some time. That’s why Timmy tries to reduce the amount of sizes that have to be generated.

When a GIF is uploaded to the backend, it will only be resized to the `thumbnail` size defined in your image configuration. GIF images in the Media Library will be loaded in a smaller size, because this makes the Media Grid load faster. All other image size are ignored when uploading an image. GIF images are still resized on the fly.

## How can I better control the markup for SVG and GIF images?

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

## How can I make the full size unavailable when an image is inserted into the WordPress Editor?

Timmy uses the filter `image_size_names_choose` with a standard priority of 10 to return the image sizes configured with Timmy and additionally adds the full size of an image. Add the following filter to your theme functions to remove the full size again. 

```php
add_filter( 'image_size_names_choose', function( $sizes ) {
    unset( $sizes['full'] );
    return $sizes;
}, 11 );
```

## How does it work again with the srcset and sizes parameters?

The `sizes` and `srcset` attribute tells the browser which images sizes are available and let’s **the browser choose which image size to display** based on the current viewport size, caching settings etc.

This image by [Harry Roberts](https://twitter.com/csswizardry/status/836960832789565440) shows a simple way to explain/remember what it means:

![](https://cloud.githubusercontent.com/assets/2084481/24998864/d938d100-203b-11e7-8233-3b0a48b81c13.jpg)

## Why is my backend unresponsive?

With Timmy’s logic, when an image size is changed in the configuration, it can trigger an on-the-fly regeneration of an image. This can lead to performance problems, because resizing an image takes quite some time. Timmy is optimized to be very performant in the backend and tries to keep resizes to the minimum.

In the following case it will always trigger a resize:

- Changing the image size of the `thumbnail` size.

When you do this, or if you see your Media takes a very long time loading images, or doesn’t load them at all, it might be a good idea to run Regenerate Thumbnails.
