<?php
/**
 * Elementor widget: Instagrid Carousel.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EWGCS_Elementor_Widget_Carousel extends \Elementor\Widget_Base {

    public function get_name() {
        return 'ewgcs_instagrid_carousel';
    }

    public function get_title() {
        return __( 'Instagrid Carousel', 'eweb-simple-gallery-instagrid' );
    }

    public function get_icon() {
        return 'eicon-slider-push';
    }

    public function get_categories() {
        return array( 'general' );
    }

    public function get_keywords() {
        return array( 'instagrid', 'gallery', 'carousel', 'portfolio' );
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            array(
                'label' => __( 'Content', 'eweb-simple-gallery-instagrid' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'posts_per_page',
            array(
                'label'   => __( 'Posts Per Page', 'eweb-simple-gallery-instagrid' ),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'default' => 12,
                'min'     => 1,
                'max'     => 100,
            )
        );

        $this->add_control(
            'slides',
            array(
                'label'   => __( 'Slides Desktop', 'eweb-simple-gallery-instagrid' ),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'default' => 4,
                'min'     => 1,
                'max'     => 6,
            )
        );

        $this->add_control(
            'show_arrows',
            array(
                'label'        => __( 'Show Arrows', 'eweb-simple-gallery-instagrid' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'eweb-simple-gallery-instagrid' ),
                'label_off'    => __( 'No', 'eweb-simple-gallery-instagrid' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'lang',
            array(
                'label'       => __( 'Language (optional)', 'eweb-simple-gallery-instagrid' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __( 'auto (pt, en, es...)', 'eweb-simple-gallery-instagrid' ),
                'default'     => '',
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings       = $this->get_settings_for_display();
        $posts_per_page = isset( $settings['posts_per_page'] ) ? intval( $settings['posts_per_page'] ) : 12;
        $slides         = isset( $settings['slides'] ) ? intval( $settings['slides'] ) : 4;
        $show_arrows    = isset( $settings['show_arrows'] ) && 'yes' === $settings['show_arrows'];
        $lang           = isset( $settings['lang'] ) ? $settings['lang'] : '';

        $query = EWGCS_Shortcode_Shared::query_feed_posts( $posts_per_page );
        if ( ! $query->have_posts() ) {
            echo '<p>No posts available.</p>';
            return;
        }

        $instance_id = 'ewgcs-carousel-' . esc_attr( $this->get_id() );

        echo '<div class="ewgcs-carousel-widget" id="' . $instance_id . '">';

        if ( $show_arrows ) {
            echo '<button type="button" class="ewgcs-carousel-arrow ewgcs-carousel-prev" aria-label="Previous">&#10094;</button>';
        }

        echo '<div class="ewgcs-all-portfolio-grid ewgcs-layout-carousel" style="--ewgcs-slides:' . esc_attr( max( 1, min( 6, $slides ) ) ) . ';">';
        while ( $query->have_posts() ) {
            $query->the_post();
            echo EWGCS_Shortcode_Shared::render_card( get_the_ID(), $lang );
        }
        echo '</div>';

        if ( $show_arrows ) {
            echo '<button type="button" class="ewgcs-carousel-arrow ewgcs-carousel-next" aria-label="Next">&#10095;</button>';
        }

        echo '</div>';

        wp_reset_postdata();
    }
}
