<?php
/**
 * Shared helpers for Instagrid shortcodes.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EWGCS_Shortcode_Shared {

    public static function normalize_lang( $lang = '' ) {
        $lang = EWGCS_Core::sanitize_lang_code( $lang );
        if ( $lang ) {
            return $lang;
        }

        return EWGCS_Core::get_current_language();
    }

    public static function render_card( $post_id, $lang = '' ) {
        $post_id = intval( $post_id );
        $lang    = self::normalize_lang( $lang );

        if ( ! $post_id ) {
            return '';
        }

        $cover_image = self::get_cover_image( $post_id, $lang );
        if ( ! $cover_image ) {
            return '<div class="ewgcs-no-image">No image available</div>';
        }

        $has_multiple = self::has_multiple_images( $post_id, $lang );
        $has_social   = self::has_social_url( $post_id, $lang );

        ob_start();
        ?>
        <a href="#" class="ewgcs-portfolio-item ewgcs-feed-item<?php echo $has_multiple ? ' ewgcs-has-multiple' : ''; ?><?php echo $has_social ? ' ewgcs-has-social' : ''; ?>" data-id="<?php echo esc_attr( $post_id ); ?>" data-lang="<?php echo esc_attr( $lang ); ?>">
            <div class="ewgcs-cover-wrapper">
                <img src="<?php echo esc_url( $cover_image['url'] ); ?>" alt="<?php echo esc_attr( $cover_image['alt'] ?: get_the_title( $post_id ) ); ?>" class="ewgcs-cover-image" loading="lazy" />
                <?php if ( $has_multiple ) : ?>
                    <span class="ewgcs-multi-icon" aria-hidden="true"></span>
                <?php endif; ?>
                <?php if ( $has_social ) : ?>
                    <span class="ewgcs-social-hint" aria-hidden="true">↗</span>
                <?php endif; ?>
            </div>
        </a>
        <?php
        return ob_get_clean();
    }

    public static function query_feed_posts( $posts_per_page ) {
        return new WP_Query(
            array(
                'post_type'              => EWGCS_Post_Type::POST_TYPE,
                'posts_per_page'         => intval( $posts_per_page ),
                'post_status'            => 'publish',
                'no_found_rows'          => true,
                'update_post_term_cache' => false,
            )
        );
    }

    public static function get_cover_image( $post_id, $lang = '' ) {
        $featured_id  = get_post_thumbnail_id( $post_id );
        $featured_url = get_the_post_thumbnail_url( $post_id, 'large' );

        if ( $featured_url ) {
            return array(
                'url' => $featured_url,
                'alt' => get_post_meta( $featured_id, '_wp_attachment_image_alt', true ) ?: get_the_title( $post_id ),
            );
        }

        // --- MULTILINGUAL TRANSLATION FALLBACK ---
        $fallback_post_id = EWGCS_Core::get_translation_fallback_post_id( $post_id );
        if ( $fallback_post_id && $fallback_post_id !== $post_id ) {
            $fallback_featured_id  = get_post_thumbnail_id( $fallback_post_id );
            $fallback_featured_url = get_the_post_thumbnail_url( $fallback_post_id, 'large' );
            if ( $fallback_featured_url ) {
                return array(
                    'url' => $fallback_featured_url,
                    'alt' => get_post_meta( $fallback_featured_id, '_wp_attachment_image_alt', true ) ?: get_the_title( $fallback_post_id ),
                );
            }
        }

        $gallery_images = EWGCS_Core::get_gallery_images( $post_id, $lang );
        if ( ! empty( $gallery_images ) ) {
            return $gallery_images[0];
        }

        return null;
    }

    public static function has_multiple_images( $post_id, $lang = '' ) {
        $ids = array();

        $featured_id = get_post_thumbnail_id( $post_id );
        if ( $featured_id ) {
            $ids[] = intval( $featured_id );
        }

        $gallery_ids = get_post_meta( $post_id, EWGCS_Core::get_gallery_meta_key( $lang ), true );
        if ( is_array( $gallery_ids ) ) {
            foreach ( $gallery_ids as $gallery_id ) {
                $ids[] = intval( $gallery_id );
            }
        }

        if ( empty( $gallery_ids ) && $lang ) {
            $default_gallery_ids = get_post_meta( $post_id, '_ewgcs_gallery_images', true );
            if ( is_array( $default_gallery_ids ) ) {
                foreach ( $default_gallery_ids as $gallery_id ) {
                    $ids[] = intval( $gallery_id );
                }
            }
        }

        $ids = array_filter( array_unique( $ids ) );
        return count( $ids ) > 1;
    }

    public static function has_social_url( $post_id, $lang = '' ) {
        $social_url = EWGCS_Core::get_social_url( $post_id, $lang );
        return ! empty( $social_url ) && filter_var( $social_url, FILTER_VALIDATE_URL );
    }
}
