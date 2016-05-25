<?php
/**
 * Plugin Name: Timmy
 * Plugin URI: https://bitbucket.org/mindkomm/timmy
 * Description: Opt-in plugin for Timber Library to make it even more convenient to work with images.
 * Version: 0.10.0
 * Author: Lukas Gächter <@lgaechter>
 * Author URI: http://www.mind.ch
 */

/**
 * Load composer_autoload files to make sure Timber Library is loaded
 *
 * Check the following folders (in that order):
 * - Current directory
 * - wp-content directory
 * - Plugins directory
 * - Child theme directory
 * - Theme directory
 */
if ( file_exists( $composer_autoload = __DIR__ . '/vendor/autoload.php' )
	 || file_exists( $composer_autoload = WP_CONTENT_DIR . '/vendor/autoload.php' )
	 || file_exists( $composer_autoload = plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' )
	 || file_exists( $composer_autoload = get_stylesheet_directory() . '/vendor/autoload.php' )
	 || file_exists( $composer_autoload = get_template_directory() . '/vendor/autoload.php' )
) {
	require_once( $composer_autoload );
}

require_once( 'functions-images.php' );
require_once( 'lib/Timmy.php' );

add_action( 'plugins_loaded', function() {
	new Timmy\Timmy();
});
