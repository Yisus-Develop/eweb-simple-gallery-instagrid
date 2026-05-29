<?php
/**
 * Elementor integration bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EWGCS_Elementor {

    public static function init() {
        add_action( 'elementor/widgets/register', array( __CLASS__, 'register_widgets' ) );
    }

    public static function register_widgets( $widgets_manager ) {
        if ( ! class_exists( '\\Elementor\\Widget_Base' ) ) {
            return;
        }

        require_once EWGCS_PATH . 'includes/widgets/class-sgc-elementor-widget-carousel.php';
        $widgets_manager->register( new EWGCS_Elementor_Widget_Carousel() );
    }
}
