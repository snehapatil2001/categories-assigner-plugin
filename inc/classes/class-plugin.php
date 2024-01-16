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
	public static function count_images( int $post_id ): int {

		// Get featured image count.
		$featured_image_count = has_post_thumbnail( $post_id ) ? 1 : 0;

		// Get post content.
		$post_content = get_post_field( 'post_content', $post_id );

		// Count images in post content.
		preg_match_all( '/<img[^>]+>/', $post_content, $img_matches );
		$img_count = count( $img_matches[0] );

		$image_count_shortcode = 0;
		// If post content has gallery shortcode.
		if ( has_shortcode( $post_content, 'gallery' ) ) {
			// Fetching all the shortcodes in the post content.
			if ( preg_match_all( '/' . get_shortcode_regex() . '/s', $post_content, $matches, PREG_SET_ORDER ) ) {
				foreach ( $matches as $shortcode ) {
					if ( 'gallery' === $shortcode[2] ) {
						$gallery = do_shortcode_tag( $shortcode );
						// Fetching src from all the images in the gallery shorcode.
						preg_match_all( '#src=([\'"])(.+?)\1#is', $gallery, $src, PREG_SET_ORDER );
						if ( ! empty( $src ) ) {
							$image_count_shortcode += count( $src );
						}
					}
				}
			}
		}

		// Total image count.
		$total_image_count = $featured_image_count + $img_count + $image_count_shortcode;

		return $total_image_count;
	}
}
