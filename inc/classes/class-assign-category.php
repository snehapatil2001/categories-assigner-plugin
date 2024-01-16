<?php

namespace PMC_Plugin\Inc\Classes\Class_Assign_Category;

use PMC_Plugin\Inc\Classes\Class_Plugin\Plugin;

if (class_exists('WPCOM_VIP_CLI_Command')) {
    /**
     * Assign_Category
     */
    class Assign_Category extends \WPCOM_VIP_CLI_Command
    {
        /**
         * Assign category to all posts.
         *
         * ## EXAMPLES
         *
         * wp assign-category update_post
         *
         * @when after_wp_load
         */
        public function update_post(array $args, array $assoc_args): void
        {
            // Disable term counting, Elasticsearch indexing, and PushPress.
            $this->start_bulk_operation();

            $posts_per_page = 100;
            $paged = 1;
            $count = 0;

            // Check if parent category exists, and create it if not.
            // phpcs:ignore
            $parent_category = term_exists('pmc', 'category');

            if (0 === $parent_category || null === $parent_category) {
                // 'pmc' category doesn't exist, create it.
                $parent_category = wp_insert_term('pmc', 'category');
                \WP_CLI::success('Parent category "pmc" created.');
            }

            // Check if child category exists, and create it if not.
            // phpcs:ignore
            $child_category = term_exists('rollingstone', 'category');

            if (0 === $child_category || null === $child_category) {
                // 'rollingstone' category doesn't exist, create it under the 'pmc' parent.
                $child_category = wp_insert_term('rollingstone', 'category', array('parent' => $parent_category['term_id']));
                \WP_CLI::success('Child category "rollingstone" created under "pmc".');
            }

            do {
                $query_args = array(
                    'posts_per_page'   => $posts_per_page,
                    'paged'            => $paged,
                    'fields'           => 'ids',
                );

                $posts_query = new \WP_Query($query_args);
                $post_ids = $posts_query->posts;

                // Assign child category 'rollingstone' to all posts.
                foreach ($post_ids as $post_id) {
                    $categories = wp_get_post_categories($post_id, array('fields' => 'slugs'));

                    // Count images from post content and featured image.
                    $image_count = Plugin::count_images($post_id);

                    // Add meta with the image count.
                    update_post_meta($post_id, '_pmc_image_counts', $image_count);

                    if (!in_array('rollingstone', $categories, true)) {
                        $current_categories = wp_get_post_categories($post_id, array('fields' => 'ids'));

                        // Remove existing categories.
                        wp_remove_object_terms($post_id, $current_categories, 'category');
                        // Assign 'rollingstone' category under 'pmc'.
                        wp_set_post_categories($post_id, array($child_category['term_id']), true);
                    }
                    $count++;
                }

                // Free up memory.
                $this->vip_inmemory_cleanup();

                $paged++;
            } while ($posts_query->have_posts());

            \WP_CLI::success(sprintf('Child category "rollingstone" assigned to %d posts.', $count));

            $this->end_bulk_operation();
        }
    }
}

