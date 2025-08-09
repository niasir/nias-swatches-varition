<?php
/**
 * Swatches and Buttons Display in Product Page
 *
 * @package Nias_Variation_Swatches
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class NS_VR_Frontend_Display {

    public function __construct() {
        add_filter( 'woocommerce_dropdown_variation_attribute_options_html', array( $this, 'get_swatches_html' ), 100, 2 );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    public function enqueue_scripts() {
        if ( is_product() ) {
            $product = wc_get_product( get_the_ID() );
            if ( $product && $product->is_type( 'variable' ) ) {
                wp_enqueue_style( 'ns-vr-frontend-style', NS_VR_PLUGIN_URL . 'assets/css/frontend.css', array(), NS_VR_VERSION );
                wp_enqueue_script( 'ns-vr-frontend-script', NS_VR_PLUGIN_URL . 'assets/js/frontend.js', array( 'jquery' ), NS_VR_VERSION, true );

                $settings = get_option('ns_vr_settings');
                wp_localize_script('ns-vr-frontend-script', 'ns_vr_settings', $settings);

                $this->add_dynamic_styles();
            }
        }
    }

    public function add_dynamic_styles() {
        $options = get_option( 'ns_vr_settings' );
        $width = isset( $options['swatch_width'] ) ? $options['swatch_width'] : '32';
        $height = isset( $options['swatch_height'] ) ? $options['swatch_height'] : '32';
        $shape = isset( $options['swatch_shape'] ) ? $options['swatch_shape'] : 'circle';
        $border_radius = ( $shape === 'circle' ) ? '50%' : '0';

        $style = "
            .ns-vr-swatch .ns-vr-swatch-color, .ns-vr-swatch .ns-vr-swatch-label {
                width: {$width}px;
                height: {$height}px;
                border-radius: {$border_radius};
            }
            .ns-vr-swatch .ns-vr-swatch-label {
                line-height: {$height}px;
            }
        ";
        wp_add_inline_style( 'ns-vr-frontend-style', $style );
    }

    public function get_swatches_html( $html, $args ) {
        $product = $args['product'];
        $attribute_name = $args['attribute'];

        $terms = $args['options'];
        if (empty($terms) && $product && $product->is_type('variable')) {
            $attributes = $product->get_variation_attributes();
            $terms = $attributes[$attribute_name];
        }

        // Determine the display type
        $attribute_id = wc_attribute_taxonomy_id_by_name( $attribute_name );
        $attribute_display_type = get_option( 'ns_vr_attribute_display_type_' . $attribute_id );

        if ( ! $attribute_display_type || $attribute_display_type === 'default' ) {
            $global_settings = get_option( 'ns_vr_settings' );
            $display_type = isset( $global_settings['default_type'] ) ? $global_settings['default_type'] : 'button';
        } else {
            $display_type = $attribute_display_type;
        }

        // Fallback for safety
        if ($display_type !== 'color' && $display_type !== 'button') {
             $display_type = 'button';
        }

        // Check if we should render swatches for this attribute type at all
        $attribute_obj = wc_get_attribute($attribute_id);
        if ($attribute_obj && $attribute_obj->type !== 'select') {
            return $html; // Return original dropdown for non-select types.
        }

        ob_start();

        wc_get_template(
            'variation-swatches.php',
            array(
                'terms'        => $terms,
                'attribute'    => $attribute_name,
                'product'      => $product,
                'args'         => $args,
                'display_type' => $display_type,
            ),
            '',
            NS_VR_PLUGIN_PATH . 'templates/'
        );

        echo '<div class="original-variation-select" style="display:none;">' . $html . '</div>';

        return ob_get_clean();
    }
}
