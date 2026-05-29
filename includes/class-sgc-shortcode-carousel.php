<?php
/**
 * Carousel feed shortcode.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EWGCS_Shortcode_Carousel {

    public static function init() {
        add_shortcode( 'instagrid_carousel', array( __CLASS__, 'render' ) );
    }

    public static function render( $atts ) {
        $atts = shortcode_atts(
            array(
                'posts_per_page' => 12,
                'slides'         => 4,
                'lang'           => '',
            ),
            $atts
        );

        $slides = max( 1, min( 6, intval( $atts['slides'] ) ) );
        $query  = EWGCS_Shortcode_Shared::query_feed_posts( $atts['posts_per_page'] );

        if ( ! $query->have_posts() ) {
            return '<p>No posts available.</p>';
        }

        ob_start();
        ?>
        <div class="ewgcs-all-portfolio-grid ewgcs-layout-carousel" style="--ewgcs-slides:<?php echo esc_attr( $slides ); ?>;">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                <?php echo EWGCS_Shortcode_Shared::render_card( get_the_ID(), $atts['lang'] ); ?>
            <?php endwhile; ?>
        </div>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }
}
