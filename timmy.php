<?php
/**
 * Plugin Name: Timmy
 * Plugin URI: https://github.com/MINDKomm/timmy/
 * Description: Advanced image manipulation for Timber.
 * Version: 0.10.4
 * Author: Lukas Gächter<@lgaechter>
 * Author URI: http://www.mind.ch
 */
require_once( 'functions-images.php' );
require_once( 'lib/Timmy.php' );

add_action( 'plugins_loaded', function() {
	new Timmy\Timmy();
});
