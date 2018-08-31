## Lazy loading

Timmy leaves it to you if you want to apply lazy loading to images and if you want to, which JavaScript library you want to use for it (for example: [lazysizes](https://github.com/aFarkas/lazysizes)). Because most of the lazy-loading libraries make use of `data` attributes to save the src and srcset attributes, Timmy provides a filter that converts your `srcset=""` into `data-srcset=""`.

```twig
<img{{ post.thumbnail|get_timber_image_responsive('custom-6')|lazy }}>
```

The `lazy` filter will only convert `srcset` attributes, but not `src` attributes. If you still need that, you can instead pass an option to the `get_timber_image_responsive()` function with your desired settings:

```twig
<img{{ post.thumbnail|get_timber_image_responsive('custom-6', {
    lazy_srcset: true,
    lazy_src: true
}) }}>
```

If you need other markup, wrap the markup that is returned from Timmy with your own filter.
