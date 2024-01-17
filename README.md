# Categories Assigner Plugin

Categories Assigner is a WordPress plugin that allows you to assign categories to all posts automatically. Additionally, it counts the images in each post, including the featured image, and adds a custom meta field for image counts.

## Features

1. **Automatic Category Assignment:**
   - Assigns the specified child category "rollingstone" under the parent category "pmc" to all posts.
   - Creates the categories if they do not exist.

2. **Image Counting:**
   - Counts all images in the post content, including the featured image.
   - Adds a custom meta field `_pmc_image_counts` to each post with the total image count.

## WP-CLI command

- **wp assign-category update_post --per-page=10**

## Installation

1. Clone the repository to your WordPress plugins directory:

   ```bash
   git clone https://github.com/your-username/categories-assigner.git wp-content/plugins/pmc-plugin
