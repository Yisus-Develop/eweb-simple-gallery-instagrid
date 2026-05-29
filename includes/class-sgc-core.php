<?php
/**
 * Core utilidades de galeria.
 * Class EWGCS_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EWGCS_Core {

    public static function init() {
        add_action( 'init', array( __CLASS__, 'on_init' ) );
    }

    public static function on_init() {
        self::register_custom_fields();
    }

    private static function register_custom_fields() {
        // Reservado para futuras extensiones.
    }

    public static function get_active_languages() {
        $langs = array();

        if ( function_exists( 'pll_languages_list' ) ) {
            $pll_langs = pll_languages_list( array( 'fields' => 'slug' ) );
            if ( is_array( $pll_langs ) ) {
                $langs = array_values( array_filter( array_map( array( __CLASS__, 'sanitize_lang_code' ), $pll_langs ) ) );
            }
        } elseif ( has_filter( 'wpml_active_languages' ) ) {
            $wpml_langs = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );
            if ( is_array( $wpml_langs ) ) {
                foreach ( $wpml_langs as $lang ) {
                    if ( ! empty( $lang['language_code'] ) ) {
                        $code = self::sanitize_lang_code( $lang['language_code'] );
                        if ( $code ) {
                            $langs[] = $code;
                        }
                    }
                }
            }
        }

        $langs = array_values( array_unique( array_filter( $langs ) ) );

        if ( empty( $langs ) ) {
            $langs[] = 'default';
        }

        return $langs;
    }

    public static function get_current_language() {
        if ( function_exists( 'pll_current_language' ) ) {
            $lang = self::sanitize_lang_code( pll_current_language( 'slug' ) );
            if ( $lang ) {
                return $lang;
            }
        }

        if ( has_filter( 'wpml_current_language' ) ) {
            $lang = self::sanitize_lang_code( apply_filters( 'wpml_current_language', null ) );
            if ( $lang ) {
                return $lang;
            }
        }

        return '';
    }

    public static function get_gallery_meta_key( $lang = '' ) {
        $lang = self::sanitize_lang_code( $lang );
        if ( ! $lang || 'default' === $lang ) {
            return '_ewgcs_gallery_images';
        }

        return '_ewgcs_gallery_images_' . $lang;
    }

    public static function get_social_meta_key( $lang = '' ) {
        $lang = self::sanitize_lang_code( $lang );
        if ( ! $lang || 'default' === $lang ) {
            return '_ewgcs_social_url';
        }

        return '_ewgcs_social_url_' . $lang;
    }

    public static function sanitize_lang_code( $lang ) {
        $lang = sanitize_key( (string) $lang );
        return trim( $lang );
    }

    /**
     * Resuelve el ID del post traducido al idioma por defecto o inglés
     * si el post actual no tiene metadatos/imágenes.
     */
    public static function get_translation_fallback_post_id( $post_id ) {
        // Polylang Fallback
        if ( function_exists( 'pll_get_post' ) ) {
            if ( function_exists( 'pll_default_language' ) ) {
                $default_lang = pll_default_language();
                $translated_id = pll_get_post( $post_id, $default_lang );
                if ( $translated_id && $translated_id !== $post_id ) {
                    return $translated_id;
                }
            }

            $en_id = pll_get_post( $post_id, 'en' );
            if ( $en_id && $en_id !== $post_id ) {
                return $en_id;
            }

            // Fallback secuencial si no hay idioma por defecto
            $langs = self::get_active_languages();
            foreach ( $langs as $l ) {
                $translated_id = pll_get_post( $post_id, $l );
                if ( $translated_id && $translated_id !== $post_id ) {
                    $meta = get_post_meta( $translated_id, '_ewgcs_gallery_images', true );
                    if ( ! empty( $meta ) && is_array( $meta ) ) {
                        return $translated_id;
                    }
                    $meta_lang = get_post_meta( $translated_id, '_ewgcs_gallery_images_' . $l, true );
                    if ( ! empty( $meta_lang ) && is_array( $meta_lang ) ) {
                        return $translated_id;
                    }
                }
            }
        }

        // WPML Fallback
        if ( has_filter( 'wpml_object_id' ) ) {
            $default_lang = apply_filters( 'wpml_default_language', null );
            if ( $default_lang ) {
                $translated_id = apply_filters( 'wpml_object_id', $post_id, 'instagrid_post', false, $default_lang );
                if ( $translated_id && $translated_id !== $post_id ) {
                    return $translated_id;
                }
            }
            $en_id = apply_filters( 'wpml_object_id', $post_id, 'instagrid_post', false, 'en' );
            if ( $en_id && $en_id !== $post_id ) {
                return $en_id;
            }
        }

        return $post_id;
    }

    /**
     * Obtener imagenes de galeria para un post.
     * Prioridad: meta idioma > meta plugin base > meta legacy > ACF > adjuntas > fallback multilenguaje.
     */
    public static function get_gallery_images( $post_id, $lang = '' ) {
        $lang        = self::sanitize_lang_code( $lang );
        $custom_meta = self::get_gallery_meta_key( $lang );
        $custom      = get_post_meta( $post_id, $custom_meta, true );

        if ( ! empty( $custom ) && is_array( $custom ) ) {
            return self::format_gallery_images( $custom );
        }

        $custom_images = get_post_meta( $post_id, '_ewgcs_gallery_images', true );
        if ( ! empty( $custom_images ) && is_array( $custom_images ) ) {
            return self::format_gallery_images( $custom_images );
        }

        $old_images = get_post_meta( $post_id, 'sgc_gallery_images', true );
        if ( ! empty( $old_images ) && is_array( $old_images ) ) {
            update_post_meta( $post_id, '_ewgcs_gallery_images', $old_images );
            delete_post_meta( $post_id, 'sgc_gallery_images' );

            return self::format_gallery_images( $old_images );
        }

        if ( function_exists( 'get_field' ) ) {
            $acf_gallery = get_field( 'galeria', $post_id );
            if ( $acf_gallery && is_array( $acf_gallery ) ) {
                return self::format_acf_gallery( $acf_gallery );
            }
        }

        $attached = self::get_attached_images( $post_id );
        if ( ! empty( $attached ) ) {
            return $attached;
        }

        // --- MULTILINGUAL TRANSLATION FALLBACK ---
        $fallback_post_id = self::get_translation_fallback_post_id( $post_id );
        if ( $fallback_post_id && $fallback_post_id !== $post_id ) {
            $fallback_images = get_post_meta( $fallback_post_id, '_ewgcs_gallery_images', true );
            if ( ! empty( $fallback_images ) && is_array( $fallback_images ) ) {
                return self::format_gallery_images( $fallback_images );
            }

            $langs = self::get_active_languages();
            foreach ( $langs as $l ) {
                $fallback_key = self::get_gallery_meta_key( $l );
                $fallback_images = get_post_meta( $fallback_post_id, $fallback_key, true );
                if ( ! empty( $fallback_images ) && is_array( $fallback_images ) ) {
                    return self::format_gallery_images( $fallback_images );
                }
            }
        }

        return array();
    }

    public static function get_social_url( $post_id, $lang = '' ) {
        $lang       = self::sanitize_lang_code( $lang );
        $social_key = self::get_social_meta_key( $lang );
        $social_url = get_post_meta( $post_id, $social_key, true );

        if ( ! empty( $social_url ) && filter_var( $social_url, FILTER_VALIDATE_URL ) ) {
            return $social_url;
        }

        $fallback = get_post_meta( $post_id, '_ewgcs_social_url', true );
        if ( ! empty( $fallback ) && filter_var( $fallback, FILTER_VALIDATE_URL ) ) {
            return $fallback;
        }

        // --- MULTILINGUAL TRANSLATION FALLBACK ---
        $fallback_post_id = self::get_translation_fallback_post_id( $post_id );
        if ( $fallback_post_id && $fallback_post_id !== $post_id ) {
            $social_url = get_post_meta( $fallback_post_id, '_ewgcs_social_url', true );
            if ( ! empty( $social_url ) && filter_var( $social_url, FILTER_VALIDATE_URL ) ) {
                return $social_url;
            }

            $langs = self::get_active_languages();
            foreach ( $langs as $l ) {
                $fallback_key = self::get_social_meta_key( $l );
                $social_url = get_post_meta( $fallback_post_id, $fallback_key, true );
                if ( ! empty( $social_url ) && filter_var( $social_url, FILTER_VALIDATE_URL ) ) {
                    return $social_url;
                }
            }
        }

        return '';
    }

    private static function format_gallery_images( $image_ids ) {
        $images = array();

        foreach ( $image_ids as $image_id ) {
            $image_src = wp_get_attachment_image_src( $image_id, 'full' );
            if ( $image_src ) {
                $images[] = array(
                    'id'     => $image_id,
                    'url'    => $image_src[0],
                    'alt'    => get_post_meta( $image_id, '_wp_attachment_image_alt', true ),
                    'width'  => $image_src[1],
                    'height' => $image_src[2],
                );
            }
        }

        return $images;
    }

    private static function format_acf_gallery( $acf_gallery ) {
        $images = array();

        foreach ( $acf_gallery as $image ) {
            if ( is_array( $image ) && isset( $image['ID'] ) ) {
                $images[] = array(
                    'id'     => $image['ID'],
                    'url'    => $image['url'] ?? wp_get_attachment_url( $image['ID'] ),
                    'alt'    => $image['alt'] ?? get_post_meta( $image['ID'], '_wp_attachment_image_alt', true ),
                    'width'  => $image['width'] ?? 0,
                    'height' => $image['height'] ?? 0,
                );
            } elseif ( is_numeric( $image ) ) {
                $image_src = wp_get_attachment_image_src( $image, 'full' );
                if ( $image_src ) {
                    $images[] = array(
                        'id'     => $image,
                        'url'    => $image_src[0],
                        'alt'    => get_post_meta( $image, '_wp_attachment_image_alt', true ),
                        'width'  => $image_src[1],
                        'height' => $image_src[2],
                    );
                }
            }
        }

        return $images;
    }

    private static function get_attached_images( $post_id ) {
        $images = array();
        $attached_images = get_children(
            array(
                'post_parent'    => $post_id,
                'post_type'      => 'attachment',
                'post_mime_type' => 'image',
                'orderby'        => 'menu_order',
                'order'          => 'ASC',
            )
        );

        foreach ( $attached_images as $attachment_id => $attachment ) {
            $image_src = wp_get_attachment_image_src( $attachment_id, 'full' );
            if ( $image_src ) {
                $images[] = array(
                    'id'     => $attachment_id,
                    'url'    => $image_src[0],
                    'alt'    => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
                    'width'  => $image_src[1],
                    'height' => $image_src[2],
                );
            }
        }

        return $images;
    }
}
