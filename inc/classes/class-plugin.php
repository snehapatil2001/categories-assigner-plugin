<?php

namespace PMC_Plugin\Inc\Classes\Class_Plugin;

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
        \WP_CLI::add_command( 'assign-category', 'PMC_Plugin\Inc\Classes\Class_Assign_Category\AssignCategory' );
	}
}
