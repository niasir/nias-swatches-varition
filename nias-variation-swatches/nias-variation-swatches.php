<?php
/**
 * Plugin Name:       متغیر پیشرفته نیاس | nias variation swatches
 * Plugin URI:        https://nias.ir/
 * Description:       افزونه انتخاب ویژگی حرفه ای محصولات برای ووکامرس
 * Version:           1.0.0
 * Author:            نیاس
 * Author URI:        https://nias.ir/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       nias-variation-swatches
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

final class Nias_Variation_Swatches {

    const VERSION = '1.0.0';
    const PREFIX = 'ns_vr';
    const TEXT_DOMAIN = 'nias-variation-swatches';

    private static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init();
    }

    private function define_constants() {
        define( 'NS_VR_VERSION', self::VERSION );
        define( 'NS_VR_PREFIX', self::PREFIX );
        define( 'NS_VR_TEXT_DOMAIN', self::TEXT_DOMAIN );
        define( 'NS_VR_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
        define( 'NS_VR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
    }

    public function includes() {
        require_once NS_VR_PLUGIN_PATH . 'includes/class-admin-menu.php';
        require_once NS_VR_PLUGIN_PATH . 'includes/class-plugin-links.php';
        require_once NS_VR_PLUGIN_PATH . 'includes/class-variation-taxonomy.php';
        require_once NS_VR_PLUGIN_PATH . 'includes/class-frontend-display.php';
    }

    public function init() {
        // It is better to instantiate classes inside a hook like `plugins_loaded`
        // to ensure all plugins are loaded and functions are available.
        add_action('plugins_loaded', array($this, 'initialize_classes'));
    }

    public function initialize_classes() {
        if (class_exists('WooCommerce')) {
            new NS_VR_Admin_Menu();
            new NS_VR_Plugin_Links();
            new NS_VR_Variation_Taxonomy();
            new NS_VR_Frontend_Display();
        } else {
            add_action('admin_notices', array($this, 'woocommerce_not_active_notice'));
        }
    }

    public function woocommerce_not_active_notice() {
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e( 'Nias Variation Swatches requires WooCommerce to be active. Please activate WooCommerce.', 'nias-variation-swatches' ); ?></p>
        </div>
        <?php
    }
}

function NVS() {
    return Nias_Variation_Swatches::instance();
}

// Initialize the plugin
NVS();
