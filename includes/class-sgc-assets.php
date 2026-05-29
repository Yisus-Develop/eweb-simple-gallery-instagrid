<?php
/**
 * Archivo de gestión de assets del plugin
 * Class EWGCS_Assets
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class EWGCS_Assets {
    private static function get_i18n_labels() {
        $settings = get_option( 'ewgcs_settings', EWGCS_Config::get_defaults() );
        $get = static function ( string $key, string $fallback ) use ( $settings ): string {
            if ( isset( $settings[ $key ] ) && is_string( $settings[ $key ] ) && '' !== trim( $settings[ $key ] ) ) {
                return $settings[ $key ];
            }
            return $fallback;
        };

        return array(
            'view_post'  => $get( 'label_view_post', __( 'View post ↗', 'eweb-simple-gallery-instagrid' ) ),
            'prev_post'  => $get( 'label_prev_post', __( 'Previous post', 'eweb-simple-gallery-instagrid' ) ),
            'next_post'  => $get( 'label_next_post', __( 'Next post', 'eweb-simple-gallery-instagrid' ) ),
            'close'      => $get( 'label_close', __( 'Close', 'eweb-simple-gallery-instagrid' ) ),
            'prev_image' => $get( 'label_prev_image', __( 'Previous image', 'eweb-simple-gallery-instagrid' ) ),
            'next_image' => $get( 'label_next_image', __( 'Next image', 'eweb-simple-gallery-instagrid' ) ),
        );
    }

    /**
     * Inicializar la carga de assets
     */
    public static function init() {
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_frontend_assets' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );
    }

    /**
     * Cargar assets en frontend
     */
    public static function enqueue_frontend_assets() {
        // Cargar GLightbox
        wp_enqueue_style(
            'glightbox-css',
            'https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css',
            array(),
            '3.2.0'
        );

        wp_enqueue_script(
            'glightbox-js',
            'https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js',
            array(),
            '3.2.0',
            true
        );

        // Cargar CSS del Modal
        wp_enqueue_style(
            'sgc-modal-style',
            EWGCS_URL . 'assets/css/modal.css',
            array(),
            EWGCS_VER
        );

        // Cargar CSS principal
        wp_enqueue_style(
            'sgc-main-style',
            EWGCS_URL . 'assets/css/main.css',
            array('glightbox-css', 'sgc-modal-style'),
            EWGCS_VER
        );

        // Cargar JavaScript principal
        wp_enqueue_script(
            'sgc-main-script',
            EWGCS_URL . 'assets/js/main.js',
            array( 'jquery', 'glightbox-js' ),
            EWGCS_VER,
            true
        );

        wp_enqueue_script(
            'sgc-carousel-script',
            EWGCS_URL . 'assets/js/carousel.js',
            array( 'jquery' ),
            EWGCS_VER,
            true
        );

        // Localizar script con parámetros necesarios
        wp_localize_script( 'sgc-main-script', 'ewgcs_params', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'ewgcs_nonce' ),
            'i18n'  => self::get_i18n_labels(),
        ) );
    }

    /**
     * Cargar assets en admin
     */
    public static function enqueue_admin_assets( $hook ) {
        if ( 'post-new.php' !== $hook && 'post.php' !== $hook ) {
            return;
        }

        $current_post_type = '';
        $screen            = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
        if ( $screen && ! empty( $screen->post_type ) ) {
            $current_post_type = $screen->post_type;
        }

        if ( ! $current_post_type && isset( $_GET['post_type'] ) ) {
            $current_post_type = sanitize_key( wp_unslash( $_GET['post_type'] ) );
        }

        if ( ! $current_post_type && isset( $_GET['post'] ) ) {
            $current_post_type = get_post_type( intval( $_GET['post'] ) );
        }

        if ( EWGCS_Post_Type::POST_TYPE !== $current_post_type ) {
            return;
        }

        // Cargar libreria multimedia de WP.
        wp_enqueue_media();

        wp_enqueue_style(
            'sgc-admin-style',
            EWGCS_URL . 'assets/css/admin.css',
            array(),
            EWGCS_VER
        );

        wp_enqueue_script(
            'sgc-admin-script',
            EWGCS_URL . 'assets/js/admin.js',
            array( 'jquery', 'jquery-ui-sortable' ),
            EWGCS_VER . '.4',
            true
        );
    }
}
