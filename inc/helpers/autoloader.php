<?php
/**
 * Custom Autoloader and Utility Functions
 *
 * This file defines a custom autoloader function that helps autoload classes based on the namespace
 * and directory structure. It also includes utility functions for registering taxonomies, post types,
 * and rendering meta boxes for a movie library WordPress plugin.
 *
 * @package pmc-plugin
 */

// This PHP function that allows you to register your own custom autoloading function.
spl_autoload_register( 'custom_autoload' );

/**
 * Custom Autoloader
 *
 * Autoloads classes based on the namespace and directory structure.
 *
 * @since 1.0.0
 *
 * @param string $class_name The class name to be autoloaded.
 * @return void
 */
function custom_autoload( $class_name ) {
	// Convert namespace separators (\ and _) to directory separators (/ and -).
	$converted_class = str_replace( array( '\\', '_' ), array( '/', '-' ), strtolower( $class_name ) );
	// Extract the class name from the path.
	$path_parts = explode( '/', $converted_class );
	array_pop( $path_parts );
	array_shift( $path_parts );
	$class_path = implode( '/', $path_parts );
	// Set the base path to the current directory.
	$base_dir = dirname( __DIR__ ) . '/';
	// Create the file path by appending the class name with the .php extension.
	$file = $base_dir . $class_path . '.php';
	// Check if the file exists and require it if it does.
	if ( file_exists( $file ) ) {
		require_once $file;
	}
}

