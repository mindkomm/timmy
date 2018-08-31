# Hooks

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

### timmy/oversize

Filters the default oversize parameters used for an image.

An oversize parameter set for an individual image size will always overwrite values set through this filter.

**Parameters**

- **$oversize_defaults**  
	*(array|bool)* Default oversize parameters. Can be a boolean to set all values in the array or an array with keys `allow` and `style_attr`. Default `array( 'allow' => false, 'style_attr' => true )`.

**Example**

```php
add_filter( 'timmy/oversize', function( $oversize ) {
    // Never set the style_attr for an image
    $oversize['style_attr'] = false;

    return $oversize;
} );

// Shortcut to set all oversize parameters to false
add_filter( 'timmy/oversize', '__return_false' );
```
