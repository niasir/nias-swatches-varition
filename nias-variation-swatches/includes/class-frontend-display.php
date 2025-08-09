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
        $attribute = $args['attribute'];

        // This function is deprecated since WC 2.4, but we keep it for compatibility.
        // For newer versions, we get terms from $args['options'].
        $terms = $args['options'];
        if (empty($terms) && $product && $product->is_type('variable')) {
            $attributes = $product->get_variation_attributes();
            $terms = $attributes[$attribute];
        }

        $attribute_tax = get_taxonomy( $attribute );

        ob_start();

        wc_get_template(
            'variation-swatches.php',
            array(
                'terms'     => $terms,
                'taxonomy'  => $attribute_tax,
                'attribute' => $attribute,
                'product'   => $product,
                'args'      => $args,
            ),
            '',
            NS_VR_PLUGIN_PATH . 'templates/'
        );

        echo '<div class="original-variation-select" style="display:none;">' . $html . '</div>';

        return ob_get_clean();
    }
}
