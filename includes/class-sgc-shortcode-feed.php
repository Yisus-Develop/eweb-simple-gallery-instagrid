<?php
/**
 * Grid feed shortcode.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EWGCS_Shortcode_Feed {

    public static function init() {
        add_shortcode( 'instagrid_feed', array( __CLASS__, 'render' ) );
    }

    public static function render( $atts ) {
        $atts = shortcode_atts(
            array(
                'posts_per_page' => 12,
                'columns'        => 3,
                'layout'         => 'grid',
                'slides'         => 4,
                'lang'           => '',
            ),
            $atts
        );

        $layout = in_array( $atts['layout'], array( 'grid', 'carousel' ), true ) ? $atts['layout'] : 'grid';
        if ( 'carousel' === $layout ) {
            return EWGCS_Shortcode_Carousel::render( $atts );
        }

        $columns = max( 1, min( 6, intval( $atts['columns'] ) ) );
        $query   = EWGCS_Shortcode_Shared::query_feed_posts( $atts['posts_per_page'] );

        if ( ! $query->have_posts() ) {
            return '<p>No posts available.</p>';
        }

        ob_start();
        ?>
        <div class="ewgcs-all-portfolio-grid ewgcs-layout-grid ewgcs-feed-columns-<?php echo esc_attr( $columns ); ?>">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                <?php echo EWGCS_Shortcode_Shared::render_card( get_the_ID(), $atts['lang'] ); ?>
            <?php endwhile; ?>
        </div>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }
}
