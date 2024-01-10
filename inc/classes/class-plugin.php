<?php

namespace PMC_Plugin\Inc\Classes\Class_Plugin;

/**
 * Plugin
 */
class Plugin {


	/**
	 * Instance of class.
	 *
	 * @var $instance
	 */
	protected static $instance;

	/**
	 * Instance of current class.
	 *
	 * @return object Instance of current class.
	 */
	public static function get_instance() {

		static $instance = false;

		if ( false === $instance ) {
			$instance = new self();
		}
		return $instance;
	}

	/**
	 * Plugin constructor.
	 *
	 * This will register commands for assigning categories.
	 */
	protected function __construct() {
		\WP_CLI::add_command( 'assign-category', 'PMC_Plugin\Inc\Classes\Class_Assign_Category\Assign_Category' );
	}

	/**
	 * Count images from post content and featured image.
	 *
	 * @param int $post_id Post ID.
	 * @return int Image count.
	 */
	public static function count_images( $post_id ) {
		// Get featured image count.
		$featured_image_count = has_post_thumbnail( $post_id ) ? 1 : 0;

		// Get post content.
		$post_content = get_post_field( 'post_content', $post_id );

		// Count images in post content.
		preg_match_all( '/<!--\s*wp:image[^>]*-->.*?<!--\s*\/wp:image\s*-->/s', $post_content, $matches );
		$content_image_count = count( $matches[0] );

		// Total image count.
		$total_image_count = $featured_image_count + $content_image_count;

		return $total_image_count;
	}
}
