<?php
/**
 * Template for displaying attributes in product page
 *
 * @package Nias_Variation_Swatches
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$attribute_name = $args['attribute'];
$options = $args['options'];
$product = $args['product'];
// $display_type is passed from the calling function in class-frontend-display.php
$selected_value = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( wp_unslash( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ) : $product->get_variation_default_attribute( $attribute_name );

?>
<div class="ns-vr-swatches-container" data-attribute-name="attribute_<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>">
    <?php
    if ( ! empty( $options ) ) {
        if ( $product && taxonomy_exists( $attribute_name ) ) {
            $terms = wc_get_product_terms( $product->get_id(), $attribute_name, array( 'fields' => 'all' ) );

            foreach ( $terms as $term ) {
                if ( ! in_array( $term->slug, $options, true ) ) {
                    continue;
                }

                $is_selected = ( $selected_value === $term->slug );
                $swatch_class = 'ns-vr-swatch';
                if ( $is_selected ) {
                    $swatch_class .= ' selected';
                }

                $swatch_class .= ' ns-vr-swatch-' . esc_attr($display_type);

                ?>
                <div class="<?php echo esc_attr( $swatch_class ); ?>" data-value="<?php echo esc_attr( $term->slug ); ?>" title="<?php echo esc_attr( $term->name ); ?>">
                    <?php
                    $color = get_term_meta( $term->term_id, 'ns_vr_color', true );
                    $color = ! empty( $color ) ? sanitize_hex_color( $color ) : '';

                    if ( $display_type === 'color' && ! empty( $color ) ) {
                        // Color type swatch
                        echo '<span class="ns-vr-swatch-color" style="background-color: ' . esc_attr( $color ) . ';"></span>';
                    } else {
                        // Button type swatch
                        echo '<span class="ns-vr-swatch-label">' . esc_html( $term->name ) . '</span>';
                    }
                    ?>
                </div>
                <?php
            }
        }
    }
    ?>
</div>
