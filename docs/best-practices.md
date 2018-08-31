## Best Practices

### Keep amount of generated images low

With Timmy, it’s easy to define a lot of image sizes. This could lead to a lot of images that have to be generated, which could make your site slow when uploading images. When you define image sizes, you can define the srcset sizes in a way where the dimensions overlap with your other image sizes.

Consider the following image configuration:

```php
add_filter( 'timmy/sizes', function( $sizes ) {
    return array(
        'thumbnail' => array(
            'resize' => array( 150, 150 ),
        ),
        'small' => array(
            'resize' => array( 370 ),
            'srcset' => array( array( 570 ) ),
        ),
        'small-crop' => array(
            'resize' => array( 370, 270 ),
        ),
        'large' => array(
            'resize' => array( 1400 ),
            'srcset' => array(
                array( 370 ),
                array( 570 ),
            )
        )
    );
} );
```

See how the image sizes use the same dimensions? By using a reduced set of dimensions throughout your configuration, you might get along with fewer image files.

### Working with Advanced Custom Fields

The functions provided by Timmy accept ACF image arrays, but they only take the value of `ID` to convert it to a TimberImage. To save performance and to prevent ACF from looping through all your defined image sizes, it’s better to **only return the image ID** in the ACF field group settings instead of the whole image array.

![](https://cloud.githubusercontent.com/assets/2084481/26151756/6fd5bf78-3b04-11e7-86ac-d7523f47684b.png)

You can also do this programmatically for all fields with type `image`:

```php
add_filter( 'acf/load_field/type=image', function( $field ) {
    $field['return_format'] = 'id';

    return $field;
} );
```
