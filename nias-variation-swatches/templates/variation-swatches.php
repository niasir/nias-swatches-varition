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
$selected_value = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( wp_unslash( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ) : $product->get_variation_default_attribute( $attribute_name );

?>
<div class="ns-vr-swatches-container" data-attribute-name="attribute_<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>">
    <?php
    if ( ! empty( $options ) ) {
        if ( $product && taxonomy_exists( $attribute_name ) ) {
            // Get terms if this is a taxonomy - ordered. We need the names and slugs.
            $terms = wc_get_product_terms( $product->get_id(), $attribute_name, array( 'fields' => 'all' ) );

            foreach ( $terms as $term ) {
                if ( ! in_array( $term->slug, $options, true ) ) {
                    continue;
                }

                $is_selected = ( $selected_value === $term->slug );
                $color = get_term_meta( $term->term_id, 'ns_vr_color', true );
                $color = ! empty( $color ) ? sanitize_hex_color( $color ) : '';

                $swatch_class = 'ns-vr-swatch';
                if ( $is_selected ) {
                    $swatch_class .= ' selected';
                }
                ?>
                <div class="<?php echo esc_attr( $swatch_class ); ?>" data-value="<?php echo esc_attr( $term->slug ); ?>" title="<?php echo esc_attr( $term->name ); ?>">
                    <?php if ( ! empty( $color ) ) : ?>
                        <span class="ns-vr-swatch-color" style="background-color: <?php echo esc_attr( $color ); ?>;"></span>
                    <?php else : ?>
                        <span class="ns-vr-swatch-label"><?php echo esc_html( $term->name ); ?></span>
                    <?php endif; ?>
                </div>
                <?php
            }
        }
    }
    ?>
</div>
