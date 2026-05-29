<?php
/**
 * Plugin Name:       EWEB - Simple Gallery Instagrid
 * Plugin URI:        https://github.com/Yisus-Develop/simple-gallery-instagrid
 * Description:       Instagram-style gallery system with custom post type, feed, and frontend modal.
 * Version:           2.0.9
 * Author:            Yisus_Dev
 * Author URI:        https://github.com/Yisus-Develop
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires at least: 6.0
 * Requires PHP:      8.1+
 * Tested up to:      6.8
 * Text Domain:       eweb-simple-gallery-instagrid
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'EWGCS_PATH', plugin_dir_path( __FILE__ ) );
define( 'EWGCS_URL', plugin_dir_url( __FILE__ ) );
define( 'EWGCS_VER', '2.0.9' );
define( 'EWGCS_FILE', __FILE__ );
define( 'EWGCS_GITHUB_USER', 'Yisus-Develop' );
define( 'EWGCS_GITHUB_REPO', 'simple-gallery-instagrid' );

require_once EWGCS_PATH . 'includes/sgc-config.php';
require_once EWGCS_PATH . 'includes/sgc-installer.php';
require_once EWGCS_PATH . 'includes/class-sgc-core.php';
require_once EWGCS_PATH . 'includes/class-sgc-assets.php';
require_once EWGCS_PATH . 'includes/class-sgc-post-type.php';
require_once EWGCS_PATH . 'includes/class-sgc-shortcode-shared.php';
require_once EWGCS_PATH . 'includes/class-sgc-shortcode-single.php';
require_once EWGCS_PATH . 'includes/class-sgc-shortcode-feed.php';
require_once EWGCS_PATH . 'includes/class-sgc-shortcode-carousel.php';
require_once EWGCS_PATH . 'includes/class-sgc-shortcode-simple.php';
require_once EWGCS_PATH . 'includes/class-sgc-portfolio-loop.php';
require_once EWGCS_PATH . 'includes/class-sgc-admin.php';
require_once EWGCS_PATH . 'includes/class-sgc-elementor.php';
require_once EWGCS_PATH . 'includes/class-eweb-github-updater.php';

class EWGCS_Plugin {
    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        add_action( 'init', array( $this, 'on_init' ) );
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
    }

    public function on_init() {
        EWGCS_Post_Type::register();
        EWGCS_Core::init();
        EWGCS_Assets::init();
        EWGCS_Shortcode_Simple::init();
        EWGCS_Portfolio_Loop::init();
        EWGCS_Elementor::init();

        if ( is_admin() ) {
            EWGCS_Admin::init();
        }
    }

    public function load_textdomain() {
        load_plugin_textdomain( 'eweb-simple-gallery-instagrid', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }
}

function ewgcs_get_instance() {
    return EWGCS_Plugin::get_instance();
}

add_action( 'plugins_loaded', 'ewgcs_get_instance' );

register_activation_hook( __FILE__, array( 'EWGCS_Installer', 'install' ) );
register_deactivation_hook( __FILE__, array( 'EWGCS_Installer', 'update' ) );

if ( class_exists( 'EWEB_GitHub_Updater' ) ) {
    new EWEB_GitHub_Updater( EWGCS_FILE, EWGCS_GITHUB_USER, EWGCS_GITHUB_REPO );
}
