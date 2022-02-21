# API

Timmy started out with functions that returned markup. But this made it harder to access data that you might need before accessing an image. All [functions](https://github.com/mindkomm/timmy/blob/master/docs/functions.md) still work fine, but with version 1.0.0, there’s a new API.

The new API works mostly with the `Timmy\Image` class. You can use it to get data about an image with a certain size and also generate the markup you need.

First, to get a Timmy image, you can use `Timmy\Timmy::get_image()`.

**PHP**

```php
<?php

use Timmy\Timmy;

$image = Timmy::get_image( $attachment_id, 'large' );
```

**Twig**

```twig
{% set image = get_timmy_image( attachment_id, 'large' ) %}
```

This function returns a `Timmy\Image` object if you pass an ID for an existing attachment and `null` if it’s not a valid attachment.

As soon as you have that image, you can start interacting with it.

```twig
{% if image %}
    {% if image.is_webp %}
        <picture>
            {{ image.picture_responsive }}
        <picture>
    {% else %}
        <img {{ image.image_responsive }}>
    {% endif %}
{% endif %
```