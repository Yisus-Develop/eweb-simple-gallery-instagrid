<?php
/**
 * Single item shortcode.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EWGCS_Shortcode_Single {

    public static function init() {
        add_shortcode( 'simple_gallery_instagrid', array( __CLASS__, 'render' ) );
    }

    public static function render( $atts ) {
        $atts = shortcode_atts(
            array(
                'id'   => get_the_ID(),
                'lang' => '',
            ),
            $atts
        );

        return EWGCS_Shortcode_Shared::render_card( intval( $atts['id'] ), $atts['lang'] );
    }
}
