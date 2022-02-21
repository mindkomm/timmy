# Extending Timmy

When you work with the image class, you may find that you need functionality that doesn’t exist in the Image class. In that case, you can extend Timmy to use your own class that extends `Timmy\Image`.

Here’s an example where we tell Timmy to use a custom TimmyImage class that we extend with an `aspect_class()` method that returns a CSS class we can use based on the aspect ratio of an image.

**functions.php**

```php
add_filter( 'timmy/image/class', function( $class ) {
    return TimmyImage::class;
} );
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
