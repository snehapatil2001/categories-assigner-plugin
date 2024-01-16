<?php

namespace PMC_Plugin\Inc\Classes\Class_Plugin;

/**
 * Plugin
 */
class Plugin
{


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
	public static function get_instance()
	{

		static $instance = false;

		if (false === $instance) {
			$instance = new self();
		}
		return $instance;
	}

	/**
	 * Plugin constructor.
	 *
	 * This will register commands for assigning categories.
	 */
	protected function __construct()
	{
		\WP_CLI::add_command('assign-category', 'PMC_Plugin\Inc\Classes\Class_Assign_Category\Assign_Category');
	}

	/**
	 * Count images from post content and featured image.
	 *
	 * @param int $post_id Post ID.
	 * @return int Image count.
	 */
	public static function count_images(int $post_id): int {

		// Get featured image count.
		$featured_image_count = has_post_thumbnail($post_id) ? 1 : 0;
	
		// Get post content.
		$post_content = get_post_field('post_content', $post_id);
	
		// Count images in post content.
		preg_match_all('/<img[^>]+>/', $post_content, $img_matches);
		$img_count = count($img_matches[0]);
	
		// Total image count.
		$total_image_count = $featured_image_count + $img_count;
	
		return $total_image_count;
	}	
}
