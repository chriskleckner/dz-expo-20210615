<?php
/**
* Plugin Name: EE Elementor Widgets
* Plugin URI: https://github.com/chriskleckner
* Description: Custom Elementor Widget Extensions
* Version: 1.0
* Author: Chris Kleckner
* Author URI: https://github.com/chriskleckner
*/


add_action( 'init', 'my_elementor_init' );
function my_elementor_init() {
	Custom_Elementor_Widgets::get_instance();
}

class Custom_Elementor_Widgets {

	protected static $instance = null;

	public static function get_instance() {
		if ( ! isset( static::$instance ) ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	protected function __construct() {

		require_once('ee-elementor-slides.php');
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );

	}

	public function register_widgets() {
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor\ee_elementor_slides() );

    }
    
}


// Create a new category section to hold the custom widgets
add_action( 'elementor/elements/categories_registered', 'add_elementor_widget_categories' );
function add_elementor_widget_categories( $elements_manager ) {

    // Add categories
    $category_prefix = 'ee-';

    $elements_manager->add_category(
        $category_prefix . 'widgets',
        [
            'title' => 'EE Widgets',
            'icon' => 'fa fa-plug',
        ]
    );

    // Reorder $categories member so EE Custom Widgets appear first
    $reorder_cats = function() use($category_prefix){
        uksort($this->categories, function($keyOne, $keyTwo) use($category_prefix){
            if(substr($keyOne, 0, 3) == $category_prefix){
                return -1;
            }
            if(substr($keyTwo, 0, 3) == $category_prefix){
                return 1;
            }
            return 0;
        });

    };
    $reorder_cats->call($elements_manager);

}