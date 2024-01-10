<?php

namespace PMC_Plugin\Inc\Classes\Class_Assign_Category;

use PMC_Plugin\Inc\Classes\Class_Plugin\Plugin;

/**
 * Assign_Category
 */
class Assign_Category {


	/**
	 * Assign category to all posts.
	 *
	 * ## EXAMPLES
	 *
	 * wp assign-category
	 *
	 * @when after_wp_load
	 */
	public function __invoke( $args, $assoc_args ) {
		// Check if parent category exists, and create it if not.
        // phpcs:ignore
        $parent_category = term_exists('pmc', 'category');

		if ( 0 === $parent_category || null === $parent_category ) {
			// 'pmc' category doesn't exist, create it.
			$parent_category = wp_insert_term( 'pmc', 'category' );
			\WP_CLI::success( 'Parent category "pmc" created.' );
		}

		// Check if child category exists, and create it if not.
        // phpcs:ignore
        $child_category = term_exists('rollingstone', 'category');

		if ( 0 === $child_category || null === $child_category ) {
			// 'rollingstone' category doesn't exist, create it under the 'pmc' parent.
			$child_category = wp_insert_term( 'rollingstone', 'category', array( 'parent' => $parent_category['term_id'] ) );
			\WP_CLI::success( 'Child category "rollingstone" created under "pmc".' );
		}

		// Fetch all posts.
		$posts = get_posts(
			array(
				'post_type'      => 'post',
				'posts_per_page' => -1,
			)
		);

		if ( empty( $posts ) ) {
			\WP_CLI::success( 'No posts found to assign categories.' );
			return;
		}

		// Assign child category 'rollingstone' to all posts.
		foreach ( $posts as $post ) {
			$categories = wp_get_post_categories( $post->ID, array( 'fields' => 'slugs' ) );

			// Count images from post content and featured image.
			$image_count = Plugin::count_images( $post->ID );

			// Add meta with the image count.
			update_post_meta( $post->ID, '_pmc_image_counts', $image_count );

			if ( ! in_array( 'rollingstone', $categories, true ) ) {
				$current_categories = wp_get_post_categories( $post->ID, array( 'fields' => 'ids' ) );

				// Remove existing categories.
				wp_remove_object_terms( $post->ID, $current_categories, 'category' );
				// Assign 'rollingstone' category under 'pmc'.
				wp_set_post_categories( $post->ID, array( $child_category['term_id'] ), true );
			}
		}

		\WP_CLI::success( 'Child category "rollingstone" assigned to all posts.' );
	}
}
