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


        update_option('thumbnail_size_w', 500);
        update_option('thumbnail_size_h', 9999);
        update_option('thumbnail_crop', false);
        update_option('medium_size_w', 1000);
        update_option('medium_size_h', 9999);
        update_option('large_size_w', 1600);
        update_option('large_size_h', 9999);
        add_image_size('small', 700, 9999);
        add_image_size('full', 2400, 9999);

        $args = array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'widgets'
        );
        add_theme_support( 'html5', $args );


        add_editor_style();

        // Figures round images on upload
        function html5_insert_image($html, $id, $caption, $title, $align, $url, $size, $alt) {
            $html5 = "<figure class='trip__image trip__image--$size trip__image--align-$align'>";
            $html5 .= wp_get_attachment_image( $id, 'large' );
            if ($caption) {
                $html5 .= "<figcaption class='trip__image-caption'>$caption</figcaption>";
            }
            $html5 .= "</figure>";
            return $html5;
        }
        add_filter( 'image_send_to_editor', 'html5_insert_image', 10, 9 );

        function wrap_gallery( $output, $atts, $content = false, $tag = false ) {
            if (!isset($atts['columns'])){
                $atts['columns'] = 3;
            }

            $output = "<div class='gallery-wrap gallery-size-" . $atts['size'] . "'><div class='gallery gallery-columns-" . $atts['columns'] . "'>";
            $ids = explode(',', $atts['ids']);
            foreach($ids as $image){
                $output .= '<div class="gallery-item">';
                $output .= wp_get_attachment_image( $image, 'medium' );
                $output .= '</div>';
            }
            $output .= '</div></div>';
            return $output;
        }
        add_filter( 'post_gallery', 'wrap_gallery', 10, 4 );

        function wpa_45815($arr){
            $arr['block_formats'] = 'Heading=h3;Sub-Heading=h4;Paragraph=p;';
            return $arr;
        }
        add_filter('tiny_mce_before_init', 'wpa_45815');

        function cc_mime_types($mimes) {
            $mimes['svg'] = 'image/svg+xml';
            return $mimes;
        }
        add_filter('upload_mimes', 'cc_mime_types');

        function wptp_add_categories_to_attachments() {
            register_taxonomy_for_object_type( 'category', 'attachment' );
        }
        add_action( 'init' , 'wptp_add_categories_to_attachments' );

        add_filter( 'jpeg_quality', create_function( '', 'return 40;' ) );

        function replace_uploaded_image($image_data) {
            // if there is no large image : return
            if (!isset($image_data['sizes']['full'])) return $image_data;

            // paths to the uploaded image and the full image
            $upload_dir = wp_upload_dir();
            $uploaded_image_location = $upload_dir['basedir'] . '/' .$image_data['file'];
            $full_image_location = $upload_dir['path'] . '/'.$image_data['sizes']['full']['file'];

            // delete the uploaded image
            unlink($uploaded_image_location);

            // rename the full image
            rename($full_image_location,$uploaded_image_location);

            // update image metadata and return them
            $image_data['width'] = $image_data['sizes']['full']['width'];
            $image_data['height'] = $image_data['sizes']['full']['height'];
            unset($image_data['sizes']['full']);

            return $image_data;
        }

        add_filter('wp_generate_attachment_metadata','replace_uploaded_image');

        parent::__construct();

    }

    function register_post_types() {
        //this is where you can register custom post types
    }

    function register_taxonomies() {

        register_taxonomy('country', array('attachment', 'post'), array(

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
                'slug' => 'countries',
                'with_front' => false,
                'hierarchical' => true
            )
        ));

        register_taxonomy('trips', array('attachment', 'post'), array(

            'hierarchical' => true,
            'labels' => array(
                'name' => _x( 'Trips', 'taxonomy general name' ),
                'singular_name' => _x( 'Trip', 'taxonomy singular name' ),
                'search_items' =>  __( 'Search Trips' ),
                'all_items' => __( 'All Trips' ),
                'parent_item' => __( 'Parent Trip' ),
                'parent_item_colon' => __( 'Parent Trip:' ),
                'edit_item' => __( 'Edit Trip' ),
                'update_item' => __( 'Update Trip' ),
                'add_new_item' => __( 'Add New Trip' ),
                'new_item_name' => __( 'New Trip Name' ),
                'menu_name' => __( 'Trips' ),
            ),

            'rewrite' => array(
                'slug' => 'trips',
                'with_front' => false,
                'hierarchical' => false
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
