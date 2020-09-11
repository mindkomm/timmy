# Functions

You can use the following functions to get your images into your template:

## Basic stuff

* [get_timber_image()](#get_timber_image) – Returns the src attribute together with optional alt and title attributes for an image.
* [get_timber_image_src()](#get_timber_image_src) – Returns the src for an image.
* [get_timber_image_srcset()](#get_timber_image_srcset) – Returns the srcset attribute based on the `srcset` entry in the image configuration for an image size.

## Responsive Images

* [get_timber_image_responsive()](#get_timber_image_responsive) – Returns the srcset, size, alt and title attributes for an image.
* [get_timber_image_responsive_src()](#get_timber_image_responsive_src) – Returns the srcset and sizes for an image. This is practically the same as *get_timber_image_responsive*, just without alt and title tags.
* [get_timber_image_responsive_acf()](#get_timber_image_responsive_acf) – Takes the field name of an ACF image as the input and returns the same output as *get_timber_image_responsive()*.

## Additional Helpers

* [get_post_thumbnail()](#get_post_thumbnail) – Returns the src, alt and title attributes for a post thumbnail at a given size.
* [get_post_thumbnail_src()](#get_post_thumbnail_src) – Returns the src for a post thumbnail. This is practically the same as *get_post_thumbnail*, just without alt and title tags.
* [make_timber_image_lazy()](#make_timber_image_lazy) - Prepares the srcset markup for lazy-loading. Replaces `srcset=""` with `data-srcset=""`.
---

## get_timber_image

`get_timber_image( int $post_id|TimberImage $timberImage, string|array $size )`

Returns the src attribute together with optional alt and title attributes for a TimberImage.

#### Usage in WordPress templates

```php
<img<?php echo get_timber_image( get_post_thumbnail_id(), 'custom-4' ); ?>>
```

#### Usage in Twig

For Twig, this function is used as a filter on the TimberImage appended with a `|`.

```twig
<img{{ post.thumbnail|get_timber_image('custom-4-crop') }}>
```

---

## get_timber_image_src

`get_timber_image_src( int $post_id|TimberImage $timber_image, string|array $size )`

Returns the src for a TimberImage.

#### Usage in WordPress templates

```php
<img src="<?php echo get_timber_image_src( get_post_thumbnail_id(), 'custom-4-crop' ); ?>">
```

#### Usage in Twig

```twig
<img src="{{ post.thumbnail|get_timber_image_src('custom-4-crop') }}">
```

---

## get_timber_image_srcset

`get_timber_image_srcset( int $post_id|TimberImage $timber_image, string|array $size )`

Returns the srcset attribute based on the `srcset` entry in the image configuration for an image size.

#### Usage in WordPress templates

```php
<img srcset="<?php echo get_timber_image_srcset( get_post_thumbnail_id(), 'teaser' ); ?>">
```

#### Usage in Twig

```twig
<img srcset="{{ post.thumbnail|get_timber_image_srcset('teaser') }}">
```

---

## get_timber_image_responsive

`get_timber_image_responsive( int $post_id|TimberImage $timber_image, string|array $size )`

Returns the srcset, size, alt and title attributes for a TimberImage. If this function is used with a SVG image, the single src will be returned instead of srcset.

#### Usage in WordPress templates

```php
<img<?php echo get_timber_image_responsive( get_post_thumbnail_id(), 'custom-6' ); ?>>
```

#### Usage in Twig

```twig
<img{{ post.thumbnail|get_timber_image_responsive('custom-6') }}>
```

---

## get_timber_image_responsive_src

Returns the srcset and sizes for a TimberImage. This is practically the same as `get_timber_image_responsive`, just without alt and title tags.

---

## get_timber_image_responsive_acf

`get_timber_image_responsive_acf( string $field_name, string|array $size )`

Takes the field name of an ACF image as the input and returns the same output as `get_timber_image_responsive()`.

#### Usage in WordPress templates

```php
<img<?php echo get_timber_image_responsive_acf( 'image', 'custom-4-crop' ); ?>>
<!-- Will use get_field( 'image' ) to get the image information -->
```

#### Usage in Twig

For Twig, you won’t use the function as a filter like in the examples above, but as a function.

```twig
<img{{ get_timber_image_responsive_acf('image', 'custom-4-crop') }}>
```

---

## get_post_thumbnail

`get_post_thumbnail( int $post_id, string|array $size = 'post-thumbnail' )`

Returns the src, alt and title attributes for a post thumbnail at a given size.

#### Usage in WordPress templates

```php
<img<?php echo get_post_thumbnail( get_post_thumbnail_id(), 'custom-6' ); ?>>
```

#### Usage in Twig

In Twig combined with Timber you will already have the post thumbnail through `post.thumbnail`. No need to wrap it in another function.

---

## get_post_thumbnail_src

`get_post_thumbnail_src( int $post_id, string|array $size = 'post-thumbnail' )`

Returns the src for a post thumbnail. This is practically the same as `get_post_thumbnail`, just without alt and title tags.

---

## make_timber_image_lazy

`make_timber_image_lazy( string $image_markup, array $attributes = ['srcset'] )`

Prepares the srcset markup for lazy-loading. Updates the attributes passed in the `$attributes` parameter with a `data-` prefix. For example `srcset` is replaced with `data-srcset`.

#### Usage in WordPress templates

```php
<img<?php echo make_timber_image_lazy(
	get_timber_image_responsive( get_post_thumbnail_id(), 'custom-6' )
); ?>>

<img<?php echo make_timber_image_lazy(
	get_timber_image_responsive( get_post_thumbnail_id(), 'custom-6' ), ['srcset', 'src', 'sizes']
); ?>>
```

#### Usage in Twig

In Twig, you can use the `lazy` filter.

```twig
{# Default, srcset only. #}
<img{{ post.thumbnail|get_timber_image_responsive('custom-6')|lazy }}>

{# Custom array of attributes #}
<img{{ post.thumbnail|get_timber_image_responsive('custom-6')|lazy(['srcset', 'src', 'sizes']) }}>
```
