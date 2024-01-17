<?php

namespace PMC_Plugin\Inc\Classes\Class_Assign_Category;

use PMC_Plugin\Inc\Classes\Class_Plugin\Plugin;

if ( class_exists( 'WPCOM_VIP_CLI_Command' ) ) {

	/**
	 * Assign_Category
	 */
	class Assign_Category extends \WPCOM_VIP_CLI_Command {

		/**
		 * Assign category to all posts.
		 *
		 * ## OPTIONS
		 *
		 * [--per-page=<number>]
		 * : The number of posts to process per page.
		 * ---
		 * default: 100
		 * ---
		 *
		 * ## EXAMPLES
		 *
		 *     wp assign-category --per-page=20
		 *
		 * @when after_wp_load
		 */
		public function update_post( array $args, array $assoc_args ): void {

			// Disable term counting, Elasticsearch indexing, and PushPress.
			$this->start_bulk_operation();

			// User-defined posts_per_page or default to 100.
			$posts_per_page = isset( $assoc_args['per-page'] ) ? absint( $assoc_args['per-page'] ) : 100;
			$paged          = 1;
			$count          = 0;

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

			do {
				$query_args = array(
					'posts_per_page'         => $posts_per_page,
					'paged'                  => $paged,
					'fields'                 => 'ids',
					'no_found_rows'          => true,  // Disable SQL_CALC_FOUND_ROWS for performance.
					'update_post_term_cache' => false,  // Skip updating term cache.
					'update_post_meta_cache' => false,  // Skip updating meta cache.
					'order'                  => 'ASC',
					'orderby'                => 'ID',
				);

				$posts_query = new \WP_Query( $query_args );
				$post_ids    = $posts_query->posts;

				// Assign categories to all posts.
				foreach ( $post_ids as $post_id ) {

					wp_set_post_categories( $post_id, array( $parent_category['term_id'], $child_category['term_id'] ), false );

					// Count images from post content and featured image.
					$image_count = Plugin::count_images( $post_id );

					// Add meta with the image count.
					update_post_meta( $post_id, '_pmc_image_counts', $image_count );

					++$count;
				}

				// Free up memory.
				$this->vip_inmemory_cleanup();

				++$paged;
			} while ( $posts_query->have_posts() );

			\WP_CLI::success( sprintf( 'Counted images and assigned categories for %d posts.', $count ) );

			$this->end_bulk_operation();
		}
	}
}
