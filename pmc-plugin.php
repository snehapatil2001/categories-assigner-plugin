<?php
/**
 * Assigning categories to post
 *
 * @package pmc-plugin
 */

/**
 * Plugin Name: Categories Assigner ( pmc-plugin )
 * Plugin URI:  https://rtCamp.com
 * Description: This plugin will help to assign categories to all posts.
 * Version:     0.1.0
 * Author:      Sneha
 * Text Domain: pmc-plugin
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package     pmc-plugin
 * @author      sneha
 * @license     GPL-2.0+
 */

define( 'PMC_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

require_once PMC_PLUGIN_PATH . '/inc/helpers/autoloader.php';

// Init plugin.
if ( defined( 'WP_CLI' ) && WP_CLI ) {

	PMC_Plugin\Inc\Classes\Class_Plugin\Plugin::get_instance();

}