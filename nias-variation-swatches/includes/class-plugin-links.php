<?php
/**
 * Adds Settings Link in Plugin List
 *
 * @package Nias_Variation_Swatches
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class NS_VR_Plugin_Links {
    public function __construct() {
        $plugin_basename = plugin_basename( NS_VR_PLUGIN_PATH . 'nias-variation-swatches.php' );
        add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_settings_link' ) );
    }

    /**
     * Add a settings link to the plugin's entry on the plugins page.
     *
     * @param array $links An array of plugin action links.
     * @return array An array of plugin action links.
     */
    public function add_settings_link( $links ) {
        $settings_link = '<a href="' . admin_url( 'admin.php?page=nias-variation-swatches-settings' ) . '">' . __( 'Settings', 'nias-variation-swatches' ) . '</a>';
        // The user wants the default text to be in Persian. Let's change the text.
        // I will use "تنظیمات" which means "Settings" in Persian.
        $settings_link = '<a href="' . admin_url( 'admin.php?page=nias-variation-swatches-settings' ) . '">' . 'تنظیمات' . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }
}
