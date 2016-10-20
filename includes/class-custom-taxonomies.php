<?php

/**
 * The core plugin class file
 *
 * Defines the functions necessary to register our custom taxonomies with
 * WordPress.
 *
 * @link       http://code.tutsplus.com/series/the-beginners-guide-to-wordpress-taxonomies--cms-706
 * @since      1.0.1
 *
 * @package    WSUWP_Plugin_iDonate_Custom_Taxonomies
 * @author     Blair Lierman <blair.lierman@wsu.edu>
 */
class WSUWP_Plugin_iDonate_Custom_Taxonomies {

	/**
	 * Initializes the plugin by registering the hooks necessary
	 * for creating our custom taxonomies within WordPress.
	 *
	 * @since    1.0.0
	 */
	public function init() {

		add_action( 'init', array( $this, 'init_taxonomies' ) );

	}

    public function init_taxonomies()
    {
        $taxonomies = array();
        
        $taxonomies[] = array(
            'name' => 'priorities',
            'post_type' => 'fund',
            'labels' => array(
                'name'          => 'Priorities',
                'singular_name' => 'Priority',
                'edit_item'     => 'Edit Priority',
                'update_item'   => 'Update Priority',
                'add_new_item'  => 'Add New Priority',
                'menu_name'     => 'Priorities'
		    )
        );

        foreach ($taxonomies as $taxonomy) {
            $this->register_custom_taxonomy($taxonomy["name"], $taxonomy["post_type"], $taxonomy["labels"]);
        }
        
    }

    private function register_custom_taxonomy($name, $post_type, $labels)
    {
        $args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'public'            => false,
            'show_ui'           => true,
			'show_admin_column' => true,
		);

		register_taxonomy( $name, $post_type, $args );
    }
}