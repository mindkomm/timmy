# Extending Timmy

When you work with the image class, you may find that you need functionality that doesn’t exist in the Image class. In that case, you can extend Timmy to use your own class that extends `Timmy\Image`.

Here’s an example where we tell Timmy to use a custom TimmyImage class that we extend with an `aspect_class()` method that returns a CSS class we can use based on the aspect ratio of an image.

**functions.php**

```php
add_filter( 'timmy/image/class', function( $class ) {
    return TimmyImage::class;
} );
```

The filter also receives the attachment ID and the image size, which you can use to pick a different class per image. This is useful if only some of your images need special treatment – images that are served from a Content Delivery Network (CDN), for example.

```php
add_filter( 'timmy/image/class', function( $class, $attachment_id ) {
    if ( get_post_meta( $attachment_id, 'my_cdn_key', true ) ) {
        return CdnImage::class;
    }

    return $class;
}, 10, 2 );
```

**TimmyImage.php**

```php
<?php

use Timmy\Image;

/**
 * Class TimmyImage
 */
class TimmyImage extends Image {
	/**
	 * Gets a CSS class based on the aspect ratio.
	 *
	 * This also works for SVG images.
	 *
	 * @return string|null
	 */
	public function aspect_class() {
		if ( $this->is_squarish() ) {
			return 'isSquare';
		} elseif ( $this->is_landscape() ) {
			return 'isLandscape';
		}

		return 'isPortrait';
	}
}
```

In Twig, you can then access the method through `image.aspect_class`:

```twig
<img class="{{ image.aspect_class }}" {# … #}>
```
