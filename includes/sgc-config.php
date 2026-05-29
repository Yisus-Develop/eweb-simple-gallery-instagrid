<?php
/**
 * Archivo de configuración del plugin
 * sgc-config.php
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Configuración predeterminada del plugin
 */
class EWGCS_Config {
    
    /**
     * Opciones predeterminadas del plugin
     */
    public static function get_defaults() {
        return array(
            // Configuración de galería
            'gallery_columns' => 3,
            'gallery_lightbox' => true,
            'gallery_transition_speed' => 300,
            
            // Configuración de comparador
            'comparator_initial_position' => 50, // Porcentaje
            'comparator_show_labels' => true,
            'comparator_label_before' => 'Antes',
            'comparator_label_after' => 'Depois',
            'label_view_post' => 'View post ↗',
            'label_prev_post' => 'Previous post',
            'label_next_post' => 'Next post',
            'label_close' => 'Close',
            'label_prev_image' => 'Previous image',
            'label_next_image' => 'Next image',
            
            // Configuración de imágenes
            'image_quality' => 85,
            'supported_formats' => array('jpg', 'jpeg', 'png', 'webp'),
            
            // Comportamiento
            'enable_cache' => true,
            'cache_duration' => 3600, // 1 hora
        );
    }
    
    /**
     * Obtener opción con valor por defecto
     */
    public static function get_option( $option, $default = null ) {
        $options = get_option( 'ewgcs_settings', self::get_defaults() );
        
        if ( $default === null ) {
            $defaults = self::get_defaults();
            $default = isset( $defaults[ $option ] ) ? $defaults[ $option ] : null;
        }
        
        return isset( $options[ $option ] ) ? $options[ $option ] : $default;
    }
    
    /**
     * Actualizar opción
     */
    public static function update_option( $option, $value ) {
        $options = get_option( 'ewgcs_settings', self::get_defaults() );
        $options[ $option ] = $value;
        update_option( 'ewgcs_settings', $options );
    }
}

// Registrar opciones en la instalación
function ewgcs_register_settings() {
    register_setting(
        'ewgcs_settings_group',
        'ewgcs_settings',
        array(
            'type' => 'array',
            'sanitize_callback' => 'ewgcs_sanitize_settings',
            'default' => EWGCS_Config::get_defaults()
        )
    );
}
add_action( 'admin_init', 'ewgcs_register_settings' );

// Sanitizar opciones
function ewgcs_sanitize_settings( $input ) {
    $sanitized = EWGCS_Config::get_defaults(); // Valores por defecto
    
    if ( is_array( $input ) ) {
        foreach ( $input as $key => $value ) {
            switch ( $key ) {
                case 'gallery_columns':
                    $sanitized[ $key ] = absint( $value );
                    if ( $sanitized[ $key ] < 1 ) $sanitized[ $key ] = 1;
                    if ( $sanitized[ $key ] > 6 ) $sanitized[ $key ] = 6;
                    break;
                    
                case 'gallery_lightbox':
                case 'comparator_show_labels':
                case 'enable_cache':
                    $sanitized[ $key ] = (bool) $value;
                    break;
                    
                case 'gallery_transition_speed':
                    $sanitized[ $key ] = absint( $value );
                    if ( $sanitized[ $key ] < 100 ) $sanitized[ $key ] = 100;
                    if ( $sanitized[ $key ] > 1000 ) $sanitized[ $key ] = 1000;
                    break;
                    
                case 'comparator_initial_position':
                    $sanitized[ $key ] = absint( $value );
                    if ( $sanitized[ $key ] < 0 ) $sanitized[ $key ] = 0;
                    if ( $sanitized[ $key ] > 100 ) $sanitized[ $key ] = 100;
                    break;
                    
                case 'comparator_label_before':
                case 'comparator_label_after':
                    $sanitized[ $key ] = sanitize_text_field( $value );
                    break;
                    
                case 'image_quality':
                    $sanitized[ $key ] = absint( $value );
                    if ( $sanitized[ $key ] < 1 ) $sanitized[ $key ] = 1;
                    if ( $sanitized[ $key ] > 100 ) $sanitized[ $key ] = 100;
                    break;

                case 'label_view_post':
                case 'label_prev_post':
                case 'label_next_post':
                case 'label_close':
                case 'label_prev_image':
                case 'label_next_image':
                    $sanitized[ $key ] = sanitize_text_field( $value );
                    break;
                    
                case 'supported_formats':
                    if ( is_array( $value ) ) {
                        $sanitized[ $key ] = array_map( 'sanitize_text_field', $value );
                    }
                    break;
                    
                case 'cache_duration':
                    $sanitized[ $key ] = absint( $value );
                    break;
            }
        }
    }
    
    return $sanitized;
}
