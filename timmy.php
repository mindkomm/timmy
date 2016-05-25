<?php
/**
 * Plugin Name: Timmy
 * Plugin URI: https://bitbucket.org/mindkomm/timmy
 * Description: Opt-in plugin for Timber Library to make it even more convenient to work with images.
 * Version: 0.10.0
 * Author: Lukas Gächter <@lgaechter>
 * Author URI: http://www.mind.ch
 */
require_once( 'functions-images.php' );
require_once( 'lib/Timmy.php' );

add_action( 'plugins_loaded', function() {
	new Timmy\Timmy();
});
