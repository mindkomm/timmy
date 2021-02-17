# Responsive Content Images

<!-- TOC -->

- [Mapping image sizes](#mapping-image-sizes)
- [Filters](#filters)
    - [timmy/responsive_content_image/attributes](#timmyresponsive_content_imageattributes)
    - [timmy/responsive_content_image](#timmyresponsive_content_image)

<!-- /TOC -->

---

Timmy can make images added to the WYSIWYG editor responsive.

- Image sizes are selectable in the editor by default. If you don’t want an image size to show up in the backend, use `'show_in_ui' => false`.
- Selects the size defined in your image configuration to build up the responsive markup through [`get_timber_image_responsive()`](#get_timber_image_responsive).

Enable the functionality in your theme by instantiating the feature in your **functions.php**:

```php
new Timmy\Responsive_Content_Images(); 
```

## Mapping image sizes

When you add an image in the editor, WordPress will save the size you add in the image markup. When you want to make changes to images on a large amount posts, it’s not practical to edit all the posts.

If you wanted to change the output size for the `small` image size, you could add a `small` image key to your image configuration. Or you could map the sizes used in the content to your image configuration.

Through the `map_sizes` option that you can pass to the `Timmy\Responsive_Content_Images()` constructor, you tell Timmy which image size it should use to generate the HTML markup. In the following example, all images added in the editor would be displayed with the `content` size:

```php
new Timmy\Responsive_Content_Images( [
    'map_sizes' => 'content',
] );
```

The `map_sizes` option also accepts an array with values, if you want to map different sizes:

```php
new Timmy\Responsive_Content_Images( [
    'map_sizes' => [
        'large'     => 'content',
        'thumbnail' => 'content-small'
    ],
] );
```

## Control WYSIWYG contents other than post content

By default, the functionality uses the `the_content` filter to filter images in all content that is run through that filter.

There are times where you might want to have better control over that. By using the `content_filters` argument in the constructor, you can pass in all the filters where responsive content image filtering should be applied.

One example for this is `acf_the_content`, which is used for [ACF WYSIWYG fields](https://www.advancedcustomfields.com/resources/wysiwyg-editor/).

```php
new Timmy\Responsive_Content_Images( [
	'content_filters' => [
		'acf_the_content' => 11,
	],
] );
```

Use an associative array where the key is the filter name and the value is the priority.

As soon as you use the `content_filters` argument, no filters will be applied by default. This means that if you want to use the `the_content` filter alongside the filters you add yourself, you need to add it again manually.

```php
new Timmy\Responsive_Content_Images( [
	'content_filters' => [
        'the_content'     => 10,
		'acf_the_content' => 11,
	],
] );
```

## Filters

### timmy/responsive_content_image/attributes

Filters image attributes used for a responsive content image.

**Parameters**

* **$attributes**  
    *(array)* A key-value array of HTML attributes.
* **$attachment_id**  
    *(int)* The attachment ID of the image.
* **$img_size**  
    *(string)* The image size key.

**Example**

Update CSS classes.

```php
add_filter( 'timmy/responsive_content_image/attributes', function( $attributes, $attachment_id, $img_size ) {
    $attributes['class'] .= 'size-' . $img_size;

    return $attributes;
}, 10, 3 );
```

---

### timmy/responsive_content_image

Filters the image HTML markup.

This filter can be used to append content to an image.

**Parameters**

* **$image**  
    *(string)* The image HTML markup.
* **$attachment_id**  
    *(int)* The attachment ID of the image.
* **$img_size**  
    *(string)* The image size key.

**Example**

Wrap the image with custom code.

```php
add_filter( 'timmy/responsive_content_image', function( $image, $attachment_id, $img_size ) {
    return Timber\Timber::compile( 'post-content-image.twig', array(
        'image' => $image,
        'id'    => $attachment_id,
    ) );
}, 10, 3 );
```

**post-content-image.twig**

```twig
<div class="fullscreen-image" id="fullscreen-image-{{ id }}">
    {{ image }}
</div>
```
