<?php

if ( ! class_exists( 'Timber' ) ) {
    add_action( 'admin_notices', function() {
            echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
        } );
    return;
}

class StarterSite extends TimberSite {

    function __construct() {
        add_theme_support( 'post-formats' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'menus' );
        add_filter( 'timber_context', array( $this, 'add_to_context' ) );
        add_filter( 'get_twig', array( $this, 'add_to_twig' ) );
        add_action( 'init', array( $this, 'register_post_types' ) );
        add_action( 'init', array( $this, 'register_taxonomies' ) );
        parent::__construct();
    }

    function register_post_types() {
        //this is where you can register custom post types
    }

    function register_taxonomies() {
        //this is where you can register custom taxonomies

        register_taxonomy('country', 'post', array(

            'hierarchical' => true,
            'labels' => array(
                'name' => _x( 'Countries', 'taxonomy general name' ),
                'singular_name' => _x( 'Country', 'taxonomy singular name' ),
                'search_items' =>  __( 'Search Countries' ),
                'all_items' => __( 'All Countries' ),
                'parent_item' => __( 'Parent Country' ),
                'parent_item_colon' => __( 'Parent Country:' ),
                'edit_item' => __( 'Edit Country' ),
                'update_item' => __( 'Update Country' ),
                'add_new_item' => __( 'Add New Country' ),
                'new_item_name' => __( 'New Country Name' ),
                'menu_name' => __( 'Countries' ),
            ),

            'rewrite' => array(
                'slug' => 'in',
                'with_front' => false,
                'hierarchical' => true
            )
        ));
    }

    function add_to_context( $context ) {
        $context['foo'] = 'bar';
        $context['stuff'] = 'I am a value set in your functions.php file';
        $context['notes'] = 'These values are available everytime you call Timber::get_context();';
        $context['menu'] = new TimberMenu();
        $context['site'] = $this;
        return $context;
    }

    function add_to_twig( $twig ) {
        /* this is where you can add your own fuctions to twig */
        $twig->addExtension( new Twig_Extension_StringLoader() );
        $twig->addFilter( 'myfoo', new Twig_Filter_Function( 'myfoo' ) );
        return $twig;
    }

}

new StarterSite();