<?php
/**
 * Archivo de gestión del panel de administración
 * Class EWGCS_Admin
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class EWGCS_Admin {

    /**
     * Inicializar funcionalidades de admin
     */
    public static function init() {
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
        add_action( 'save_post', array( __CLASS__, 'save_meta_box_data' ) );
        add_action( 'admin_menu', array( __CLASS__, 'register_help_submenu' ) );
    }

    /**
     * Registrar submenu de ayuda/uso.
     */
    public static function register_help_submenu() {
        add_submenu_page(
            'edit.php?post_type=' . EWGCS_Post_Type::POST_TYPE,
            __( 'Como Usar Instagrid', 'eweb-simple-gallery-instagrid' ),
            __( 'Como Usar', 'eweb-simple-gallery-instagrid' ),
            'edit_posts',
            'instagrid-how-to-use',
            array( __CLASS__, 'render_help_page' )
        );
    }

    /**
     * Renderizar pagina de ayuda.
     */
    public static function render_help_page() {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__( 'Como Usar Instagrid', 'eweb-simple-gallery-instagrid' ) . '</h1>';
        echo '<p>' . esc_html__( 'Usa estos shortcodes para mostrar las galerias:', 'eweb-simple-gallery-instagrid' ) . '</p>';
        echo '<ul style="list-style:disc;padding-left:20px;">';
        echo '<li><strong>Feed completo:</strong> <code>[instagrid_feed]</code></li>';
        echo '<li><strong>Feed con opciones:</strong> <code>[instagrid_feed posts_per_page="12" columns="3"]</code></li>';
        echo '<li><strong>Feed por idioma:</strong> <code>[instagrid_feed lang="en"]</code> (si no pones <code>lang</code>, detecta idioma actual)</li>';
        echo '<li><strong>Item unico:</strong> <code>[simple_gallery_instagrid id="123"]</code></li>';
        echo '</ul>';
        echo '<p>' . esc_html__( 'En Elementor, pega el shortcode en el widget "Shortcode".', 'eweb-simple-gallery-instagrid' ) . '</p>';
        echo '<p>' . esc_html__( 'La imagen principal usa la destacada del post. Si no existe, toma la primera imagen de la galeria.', 'eweb-simple-gallery-instagrid' ) . '</p>';
        echo '</div>';
    }

    /**
     * Añadir metabox para gestión de imágenes
     */
    public static function add_meta_boxes() {
        add_meta_box(
            'ewgcs_project_images',
            __( 'Galeria do Post', 'eweb-simple-gallery-instagrid' ),
            array( __CLASS__, 'render_meta_box' ),
            EWGCS_Post_Type::POST_TYPE,
            'normal',
            'high'
        );
    }

    private static function get_gallery_images_by_lang( $post_id, $lang ) {
        $gallery_key    = EWGCS_Core::get_gallery_meta_key( $lang );
        $gallery_images = get_post_meta( $post_id, $gallery_key, true );

        if ( ! is_array( $gallery_images ) || empty( $gallery_images ) ) {
            if ( 'default' === $lang ) {
                $old_gallery = get_post_meta( $post_id, 'sgc_gallery_images', true );
                if ( ! empty( $old_gallery ) && is_array( $old_gallery ) ) {
                    $gallery_images = $old_gallery;
                    update_post_meta( $post_id, '_ewgcs_gallery_images', $gallery_images );
                    delete_post_meta( $post_id, 'sgc_gallery_images' );
                }
            }
        }

        if ( ! is_array( $gallery_images ) || empty( $gallery_images ) ) {
            if ( 'default' === $lang ) {
                $attached_images = get_children( array(
                    'post_parent'    => $post_id,
                    'post_type'      => 'attachment',
                    'post_mime_type' => 'image',
                    'orderby'        => 'menu_order',
                    'order'          => 'ASC',
                    'numberposts'    => -1,
                ) );

                if ( ! empty( $attached_images ) ) {
                    $gallery_images = array_keys( $attached_images );
                    update_post_meta( $post_id, '_ewgcs_gallery_images', $gallery_images );
                } else {
                    $gallery_images = array();
                }
            } else {
                $gallery_images = array();
            }
        }

        return $gallery_images;
    }

    /**
     * Renderizar metabox de imágenes
     */
    public static function render_meta_box( $post ) {
        wp_nonce_field( 'ewgcs_save_meta_box', 'ewgcs_meta_box_nonce' );

        $languages = EWGCS_Core::get_active_languages();
        echo '<div class="sgc-admin-interface">';

        if ( count( $languages ) > 1 ) {
            echo '<div class="sgc-lang-tabs" style="display:flex;gap:8px;margin-bottom:12px;flex-wrap:wrap;">';
            foreach ( $languages as $idx => $lang ) {
                $is_active = 0 === $idx ? 'style="background:#2271b1;color:#fff;"' : '';
                echo '<button type="button" class="button sgc-lang-tab" data-lang="' . esc_attr( $lang ) . '" ' . $is_active . '>' . esc_html( strtoupper( $lang ) ) . '</button>';
            }
            echo '</div>';
        }

        foreach ( $languages as $idx => $lang ) {
            $gallery_images = self::get_gallery_images_by_lang( $post->ID, $lang );
            $social_url     = get_post_meta( $post->ID, EWGCS_Core::get_social_meta_key( $lang ), true );
            if ( empty( $social_url ) ) {
                $social_url = get_post_meta( $post->ID, '_ewgcs_social_url', true );
            }
            $section_style  = 0 === $idx ? '' : 'display:none;';

            echo '<div class="sgc-lang-panel" data-lang="' . esc_attr( $lang ) . '" style="' . esc_attr( $section_style ) . '">';
            echo '<div class="sgc-gallery-field">';
            echo '<div style="margin-bottom:16px;">';
            echo '<label style="display:block;font-weight:600;margin-bottom:6px;">' . esc_html__( 'Link da publicação (Instagram/Facebook/etc.)', 'eweb-simple-gallery-instagrid' ) . ' (' . esc_html( strtoupper( $lang ) ) . ')</label>';
            echo '<input type="url" name="ewgcs_social_url[' . esc_attr( $lang ) . ']" value="' . esc_attr( $social_url ) . '" placeholder="https://instagram.com/p/..." style="width:100%;max-width:700px;" />';
            echo '</div>';

            echo '<h4>' . __( 'Galeria de Imagens', 'eweb-simple-gallery-instagrid' ) . ' (' . esc_html( strtoupper( $lang ) ) . ')</h4>';
            echo '<div class="sgc-image-grid" data-lang="' . esc_attr( $lang ) . '">';

            foreach ( $gallery_images as $image_id ) {
                $image_src = wp_get_attachment_image_src( $image_id, 'thumbnail' );
                if ( ! $image_src ) {
                    continue;
                }

                $image_file     = get_attached_file( $image_id );
                $image_filename = $image_file ? basename( $image_file ) : get_the_title( $image_id );

                echo '<div class="sgc-image-item" data-id="' . intval( $image_id ) . '">';
                echo '<div class="sgc-drag-handle" title="Arrastar para ordenar">⋮⋮</div>';
                echo '<img src="' . esc_url( $image_src[0] ) . '" alt="" />';
                echo '<div class="sgc-image-name">' . esc_html( $image_filename ) . '</div>';
                echo '<button type="button" class="sgc-remove-image button">' . __( 'Eliminar', 'eweb-simple-gallery-instagrid' ) . '</button>';
                echo '<input type="hidden" name="ewgcs_gallery_images[' . esc_attr( $lang ) . '][]" value="' . intval( $image_id ) . '" />';
                echo '</div>';
            }

            echo '</div>';
            echo '<button type="button" class="button sgc-add-gallery-image" data-lang="' . esc_attr( $lang ) . '">' . __( 'Adicionar Imagens à Galeria', 'eweb-simple-gallery-instagrid' ) . '</button>';
            echo '</div>';
            echo '</div>';
        }

        echo '</div>';
    }

    /**
     * Guardar datos del metabox
     */
    public static function save_meta_box_data( $post_id ) {
        if ( get_post_type( $post_id ) !== EWGCS_Post_Type::POST_TYPE ) {
            return;
        }

        if ( ! isset( $_POST['ewgcs_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['ewgcs_meta_box_nonce'], 'ewgcs_save_meta_box' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $languages = EWGCS_Core::get_active_languages();

        if ( isset( $_POST['ewgcs_gallery_images'] ) && is_array( $_POST['ewgcs_gallery_images'] ) ) {
            foreach ( $languages as $lang ) {
                $images_for_lang = array();
                if ( isset( $_POST['ewgcs_gallery_images'][ $lang ] ) && is_array( $_POST['ewgcs_gallery_images'][ $lang ] ) ) {
                    $images_for_lang = array_map( 'intval', $_POST['ewgcs_gallery_images'][ $lang ] );
                }

                $gallery_key = EWGCS_Core::get_gallery_meta_key( $lang );
                if ( ! empty( $images_for_lang ) ) {
                    update_post_meta( $post_id, $gallery_key, $images_for_lang );
                } else {
                    delete_post_meta( $post_id, $gallery_key );
                }
            }

            if ( isset( $_POST['ewgcs_gallery_images'][0] ) ) {
                $legacy_gallery_images = array_map( 'intval', $_POST['ewgcs_gallery_images'] );
                update_post_meta( $post_id, '_ewgcs_gallery_images', $legacy_gallery_images );
            }
        } else {
            foreach ( $languages as $lang ) {
                delete_post_meta( $post_id, EWGCS_Core::get_gallery_meta_key( $lang ) );
            }
            delete_post_meta( $post_id, '_ewgcs_gallery_images' );
        }

        if ( isset( $_POST['ewgcs_social_url'] ) && is_array( $_POST['ewgcs_social_url'] ) ) {
            foreach ( $languages as $lang ) {
                $url = '';
                if ( isset( $_POST['ewgcs_social_url'][ $lang ] ) ) {
                    $url = esc_url_raw( wp_unslash( $_POST['ewgcs_social_url'][ $lang ] ) );
                }

                $social_key = EWGCS_Core::get_social_meta_key( $lang );
                if ( ! empty( $url ) ) {
                    update_post_meta( $post_id, $social_key, $url );
                } else {
                    delete_post_meta( $post_id, $social_key );
                }
            }
        } elseif ( isset( $_POST['ewgcs_social_url'] ) ) {
            $social_url = esc_url_raw( wp_unslash( $_POST['ewgcs_social_url'] ) );
            if ( ! empty( $social_url ) ) {
                update_post_meta( $post_id, '_ewgcs_social_url', $social_url );
            } else {
                delete_post_meta( $post_id, '_ewgcs_social_url' );
            }
        }

        // Cleanup de metadatos legacy de comparación
        delete_post_meta( $post_id, '_ewgcs_compare_pairs' );
        delete_post_meta( $post_id, 'sgc_comparison_pairs' );
    }
}
