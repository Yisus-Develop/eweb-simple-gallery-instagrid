<?php
/**
 * Registro del Custom Post Type de Instagrid.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EWGCS_Post_Type {

    const POST_TYPE = 'instagrid_post';

    public static function init() {
        add_action( 'init', array( __CLASS__, 'register' ) );
    }

    public static function register() {
        $labels = array(
            'name'               => __( 'Instagrid Posts', 'eweb-simple-gallery-instagrid' ),
            'singular_name'      => __( 'Instagrid Post', 'eweb-simple-gallery-instagrid' ),
            'add_new'            => __( 'Add New', 'eweb-simple-gallery-instagrid' ),
            'add_new_item'       => __( 'Add New Post', 'eweb-simple-gallery-instagrid' ),
            'edit_item'          => __( 'Edit Post', 'eweb-simple-gallery-instagrid' ),
            'new_item'           => __( 'New Post', 'eweb-simple-gallery-instagrid' ),
            'view_item'          => __( 'View Post', 'eweb-simple-gallery-instagrid' ),
            'search_items'       => __( 'Search Posts', 'eweb-simple-gallery-instagrid' ),
            'not_found'          => __( 'No posts found', 'eweb-simple-gallery-instagrid' ),
            'not_found_in_trash' => __( 'No posts found in trash', 'eweb-simple-gallery-instagrid' ),
            'menu_name'          => __( 'Instagrid', 'eweb-simple-gallery-instagrid' ),
        );

        register_post_type(
            self::POST_TYPE,
            array(
                'labels'             => $labels,
                'public'             => true,
                'show_ui'            => true,
                'show_in_menu'       => true,
                'show_in_rest'       => true,
                'menu_icon'          => 'dashicons-format-gallery',
                'has_archive'        => true,
                'rewrite'            => array( 'slug' => 'instagrid' ),
                'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
                'publicly_queryable' => true,
            )
        );
    }
}
