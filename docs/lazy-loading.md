# Lazy loading

Timmy applies lazy loading for images using a `loading="lazy"` attribute by default. If you want to apply lazy loading using `data-*` attributes, there are also ways to control that.

## Native lazy loading

If lazy loading is enabled in your WordPress setup, then Timmy will automatically add all relevant attributes for [lazy loading](https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/loading) to your image markup.

It adds the `loading` attribute with `lazy` as a value. And because a browser doesn’t know your image dimensions, it also adds `width` and `height` attributes. This prevents layout shifts (without having to use aspect ratio techniques like [the padding-bottom trick](https://css-tricks.com/aspect-ratio-boxes/)).

```html
<img src="…" loading="lazy" width="1200" height="630">
```

### Change loading attribute value

By default, the `loading` attribute will use `lazy` as a value. This defers loading of the image until it reaches a calculated distance from the viewport.

You can also use `eager` as a value to load the resource immediately, regardless of where it’s located on the page.

**Twig**

```twig
<img {{ post.thumbnail|get_timber_image_responsive('large', {
    loading: 'eager'
}) }}>
```

**PHP**

```php
<img <?php echo get_timber_image_responsive( $post->thumbnail(), 'large', [
    'loading' => 'eager',
]); ?>>
```

## Controlling width and height attributes

Timmy automatically applies `width` and `height` attributes so that browsers know in advance how big an image will be displayed and so that they can calculate the aspect ratio from these dimensions. Read more about this in Smashing Magazine’s article [Setting Height And Width On Images Is Important Again](https://www.smashingmagazine.com/2020/03/setting-height-width-images-important-again/).

The values for `width` and `height` will correspond to the size you define in `resize` in your [image configuration](https://github.com/mindkomm/timmy/blob/master/docs/image-configuration.md). When you only define a width, the height will be calculated from the aspect ratio of the image and vice versa.

Because `width` and `height` are [presentational attributes](https://css-tricks.com/presentation-attributes-vs-inline-styles/), any CSS that sets the width or height will override them, because they have such a low specificty.

The following CSS will make an image stretch to the full available width.

```css
img {
    width: 100%;
    height: auto;
}
```

Timmy will automatically add a `style` attribute to constrain the width of an image if it’s smaller than the width you want it to display at. And you can control that with the [upscale](https://github.com/mindkomm/timmy/blob/master/docs/image-configuration.md#upscale) configuration parameter.

If you need to disable the width and height attributes for single images, you can use `attr_width` and `attr_height`.

**Twig**

```twig
<img {{ post.thumbnail|get_timber_image_responsive('large', {
    attr_width: false,
    attr_height: false
}) }}>
```

**PHP**

```php
<img <?php echo get_timber_image_responsive( $post->thumbnail(), 'large', [
    'attr_width'  => false,
    'attr_height' => false,
]); ?>>
```

## Disable lazy loading

Timmy uses the [`wp_lazy_loading_enabled()`](https://developer.wordpress.org/reference/functions/wp_lazy_loading_enabled/) function to determine whether lazy loading should be applied automatically. If you want to disable lazy loading for Timmy, you can hook into the `wp_lazy_loading_enabled` filter. Timmy will use `timmy` as a `$context`.

**functions.php**

```php
add_filter( 'wp_lazy_loading_enabled', function( $is_enabled, $tag_name, $context ) {
    if ( 'timmy' === $context ) {
        return false;
    }

    return $is_enabled;
}, 10, 3 );
```

You can also disable lazy loading per instance. Pass in `false` as a value for `loading` when you use the `get_timber_image_responsive()` or `get_timber_image_responsive_src()` function:

**Twig**

```twig
<img {{ post.thumbnail|get_timber_image_responsive('large', {
    loading: false
}) }}>
```

**PHP**

```php
<img <?php echo get_timber_image_responsive( $post->thumbnail(), 'large', [
    'loading' => false,
]); ?>>
```

## JavaScript lazy loading

As an alternative to native lazy loading, you can use a JavaScript library for lazy-loading (for example: [lazysizes](https://github.com/aFarkas/lazysizes)) or [lozad](https://github.com/ApoorvSaxena/lozad.js). This was a popular technique when the native `loading` attribute still wasn’t supported very well by browsers.

Because most of the lazy-loading libraries make use of `data` attributes to save the src and srcset attributes, Timmy provides a filter that converts your `srcset=""` attributes into `data-srcset=""`.

```twig
<img{{ post.thumbnail|get_timber_image_responsive('custom-6')|lazy }}>
```

The `lazy` filter will only convert `srcset` attributes, but not `src` attributes. If you still need that, you can instead pass an option to the `get_timber_image_responsive()` function with your desired settings. Additionally to `lazy_src`, you can also pass `lazy_sizes` to convert the `sizes` attribute to `data-sizes`.

```twig
<img{{ post.thumbnail|get_timber_image_responsive('custom-6', {
    lazy_srcset: true,
    lazy_src: true,
    lazy_sizes: true
}) }}>
```

## Remarks

- If you need other markup, wrap the markup that is returned from Timmy with your own filter.
- If you use [Picturefill](https://scottjehl.github.io/picturefill/), then you need to call `picturefill()` after the images were lazy loaded to make them work in browsers that don’t support responsive images by default. Most lazy loading libraries provide a callback for that.
