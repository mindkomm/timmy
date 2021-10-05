# Hooks

- [Filters](#filters)
	- [timmy/sizes](#timmysizes)
	- [timmy/resize/ignore](#timmyresizeignore)
	- [timmy/generate_srcset_sizes](#timmygenerate_srcset_sizes)
	- [timmy/upscale](#timmyupscale)
	- [timmy/use_src_default](#timmyuse_src_default)
	- [timmy/src_default](#timmysrc_default)

## Filters

### timmy/sizes

Filters the image sizes used in Timmy. Read more about this in [Image Configuration](./image-configuration.md).

**Parameters**

- **$sizes**  
	*(array)* Image configuration array. Default `array()`.

---

### timmy/resize/ignore

Filters whether we should resize an image size.

When true is returned in this filter, the function will bailout early and the image will not be processed further.

**Parameters**

- **$ignore**  
	*(bool)* Whether to ignore an image size. Default `false`.
- **$attachment**  
	*(string)* The attachment post.
- **$size**  
	*(string)* The requested image size.
- **$file_src**  
	*(string)* The file src URL.

**Example**

The following filter is already included in Timmy by default. 

```php
add_filter( 'timmy/resize/ignore', function( $return, $attachment ) {
    // Ignore GIF images
    if ( 'image/gif' === $attachment->post_mime_type ) {
    	return true;
    }
    
    return $return;
}, 10, 2 );
```

---

### timmy/generate_srcset_sizes

Filters whether srcset sizes should be generated when an image is uploaded.

**Parameters**

- **$generate_srcset_sizes**  
    *(bool)* Whether to generate srcset sizes. Passing false will prevent srcset sizes to generated when an image is uploaded. Default `false`.
- **$key**  
	*(string)* The image size key.
- **$img_size**  
    *(array)* The image size configuration array.
- **$attachment**  
    *(WP_Post)* The attachment post.

**Example**

```php
// Generate srcset sizes for all image sizes
add_filter( 'timmy/generate_srcset_sizes', '__return_true' );
```

---

### timmy/upscale

Filters the default upscale parameters used for an image.

An upscale parameter set for an individual image size will always overwrite values set through this filter.

**Parameters**

- **$upscale_defaults**  
	*(array|bool)* Default upscale parameters. Can be a boolean to set all values in the array or an associative array with keys `allow` and `style_attr`. Default `array( 'allow' => false, 'style_attr' => true )`.

**Example**

```php
add_filter( 'timmy/upscale', function( $upscale ) {
    // Never set the style_attr for an image
    $upscale['style_attr'] = false;

    return $upscale;
} );

// Shortcut to set all upscale parameters to false
add_filter( 'timmy/upscale', '__return_false' );
```

### timmy/use_src_default

Filters whether a default src attribute should be added as a fallback.
If this filter returns `true` (the default), then a base64 string will be used as a fallback to
prevent double downloading images in older browsers. If this filter returns `false`, then no src
attribute will be added to the image. Use the `timmy/src_default` filter to define what should be
used as the src attribute’s value.

**Parameters**

- **$use_src_default**  
	*(bool)* Whether to apply the fallback. Default `true`.

**Example**

```php
// Disable default src attribute.
add_filter( 'timmy/use_src_default', '__return_false' );
```

### timmy/src_default

Filters the src default.

**Parameters**

- **$src_default**  
	*(string)* Src default. Default `data:image/gif;base64,R0lGODlhAQABAAAAADs=`.
- **$attributes**  
    *(array)* An array of helpful attributes in the filter.  
    - **$default_src**  
        *(string)* The default src for the image.
    - **$timber_image**  
        *(\Timber\Image)* Timber image instance.
    - **$size**  
        *(string)* The requested image size.
    - **$img_size**  
        *(array)* The image size configuration.
    - **$attributes**  
        *(array)* Attributes for the image markup.

**Example**

```php
// Use the default src URL as a fallback.
add_filter( 'timmy/src_default', function( $src_default, $attributes ) {
    return $attributes['default_src'];
}, 10, 2 );
```
