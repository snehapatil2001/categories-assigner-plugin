<?php

namespace PMC_Plugin\Inc\Classes\Class_Assign_Category;

class AssignCategory
{

    /**
     * Assign category to all posts.
     *
     * ## OPTIONS
     *
     * [--dry-run]
     * : Output the categories that would be assigned, but don't actually assign them.
     *
     * ## EXAMPLES
     *
     *     wp assign-category
     *
     * @when after_wp_load
     */
    public function __invoke($args, $assoc_args)
    {

        // Check if categories exist, and create them if not.
        $parent_category = term_exists('pmc', 'category');

        if ($parent_category === 0 || $parent_category === null) {
            // 'pmc' category doesn't exist, create it.
            $parent_category = wp_insert_term('pmc', 'category');
            \WP_CLI::success('Parent category "pmc" created.');
        }

        // Check if child category exists, and create it if not.
        $child_category = term_exists('rollingstone', 'category');

        if ($child_category === 0 || $child_category === null) {
            // 'rollingstone' category doesn't exist, create it under the 'pmc' parent.
            $child_category = wp_insert_term('rollingstone', 'category', array('parent' => $parent_category['term_id']));
            \WP_CLI::success('Child category "rollingstone" created under "pmc".');
        }

        // Fetch all posts.
        $posts = get_posts(array('post_type' => 'post', 'posts_per_page' => -1));

        if (empty($posts)) {
            WP_CLI::success('No posts found to assign categories.');
            return;
        }
    }
}
