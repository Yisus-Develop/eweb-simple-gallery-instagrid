<?php
/**
 * Backward-compatibility facade for shortcode registration.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EWGCS_Shortcode_Simple {

    public static function init() {
        EWGCS_Shortcode_Single::init();
        EWGCS_Shortcode_Feed::init();
        EWGCS_Shortcode_Carousel::init();
    }
}
