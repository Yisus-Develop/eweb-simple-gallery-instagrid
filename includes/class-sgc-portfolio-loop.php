<?php
/**
 * AJAX handlers para modal de galeria.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EWGCS_Portfolio_Loop {

    public static function init() {
        add_action( 'wp_ajax_ewgcs_get_project_details', array( __CLASS__, 'ajax_get_project_details' ) );
        add_action( 'wp_ajax_nopriv_ewgcs_get_project_details', array( __CLASS__, 'ajax_get_project_details' ) );
    }

    public static function ajax_get_project_details() {
        check_ajax_referer( 'ewgcs_nonce', 'nonce' );

        $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
        $lang    = isset( $_POST['lang'] ) ? EWGCS_Core::sanitize_lang_code( wp_unslash( $_POST['lang'] ) ) : '';
        if ( ! $lang ) {
            $lang = EWGCS_Core::get_current_language();
        }
        if ( ! $post_id ) {
            wp_send_json_error( 'Invalid post ID' );
        }

        $post = get_post( $post_id );
        if ( ! $post || EWGCS_Post_Type::POST_TYPE !== $post->post_type ) {
            wp_send_json_error( 'Invalid post type' );
        }

        $featured_image = '';
        $thumb_id       = get_post_thumbnail_id( $post_id );
        if ( $thumb_id ) {
            $src = wp_get_attachment_image_src( $thumb_id, 'full' );
            if ( $src ) {
                $featured_image = $src[0];
            }
        }

        $gallery_raw = EWGCS_Core::get_gallery_images( $post_id, $lang );
        $gallery     = array();
        foreach ( $gallery_raw as $img ) {
            if ( ! empty( $img['url'] ) ) {
                $gallery[] = $img['url'];
            }
        }

        if ( empty( $gallery ) && $featured_image ) {
            $gallery[] = $featured_image;
        }

        wp_send_json_success(
            array(
                'id'             => $post_id,
                'title'          => get_the_title( $post_id ),
                'featured_image' => $featured_image,
                'gallery'        => $gallery,
                'social_url'     => esc_url_raw( EWGCS_Core::get_social_url( $post_id, $lang ) ),
            )
        );
    }
}
