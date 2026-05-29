<?php
/**
 * Archivo de instalación del plugin
 * sgc-installer.php
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Clase para manejar la instalación y actualización del plugin
 */
class EWGCS_Installer {

    /**
     * Versión actual del plugin
     */
    const VERSION_OPTION = 'ewgcs_version';
    
    /**
     * Instalar el plugin
     */
    public static function install() {
        // Actualizar versión
        update_option( self::VERSION_OPTION, EWGCS_VER );
        
        // Crear cualquier dato necesario
        self::create_default_data();
        
        // Flush rewrites si es necesario
        flush_rewrite_rules();
    }
    
    /**
     * Desinstalar el plugin
     */
    public static function uninstall() {
        // Opcional: limpiar datos si es necesario
        // delete_option( self::VERSION_OPTION );
    }
    
    /**
     * Actualizar el plugin
     */
    public static function update() {
        $installed_ver = get_option( self::VERSION_OPTION );
        
        if ( $installed_ver !== EWGCS_VER ) {
            // Realizar actualizaciones específicas según versión
            if ( version_compare( $installed_ver, '1.0.0', '<' ) ) {
                self::update_to_1_0_0();
            }
            
            // Actualizar versión
            update_option( self::VERSION_OPTION, EWGCS_VER );
        }
    }
    
    /**
     * Crear datos por defecto
     */
    private static function create_default_data() {
        // No se requieren datos por defecto específicos para esta versión
    }
    
    /**
     * Actualizar a la versión 1.0.0
     */
    private static function update_to_1_0_0() {
        // Actualizaciones específicas para la versión 1.0.0
        // Asegurar que los campos personalizados estén correctamente registrados
    }
}
