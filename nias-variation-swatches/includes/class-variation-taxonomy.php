<?php
/**
 * Attribute Management and Display
 *
 * @package Nias_Variation_Swatches
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class NS_VR_Variation_Taxonomy {
    public function __construct() {
        $attribute_taxonomies = wc_get_attribute_taxonomies();
        if ( ! empty( $attribute_taxonomies ) ) {
            foreach ( $attribute_taxonomies as $tax ) {
                $taxonomy_name = wc_attribute_taxonomy_name( $tax->attribute_name );
                // Only add color picker for attributes that are of type 'select'
                if ( $tax->attribute_type === 'select' ) {
                    add_action( "{$taxonomy_name}_add_form_fields", array( $this, 'add_color_picker_field' ) );
                    add_action( "{$taxonomy_name}_edit_form_fields", array( $this, 'edit_color_picker_field' ), 10, 2 );

                    add_action( "created_{$taxonomy_name}", array( $this, 'save_color_picker_field' ), 10, 2 );
                    add_action( "edited_{$taxonomy_name}", array( $this, 'save_color_picker_field' ), 10, 2 );
                }
            }
        }

        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    public function enqueue_scripts( $hook ) {
        $screen = get_current_screen();
        if ( $screen && (strpos($screen->id, 'pa_') !== false) && ($hook === 'term.php' || $hook === 'edit-tags.php') ) {
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );
            add_action('admin_footer', array($this, 'add_color_picker_js'));
        }
    }

    public function add_color_picker_js() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                $('.ns-vr-color-picker').wpColorPicker();
            });
        </script>
        <?php
    }

    public function add_color_picker_field() {
        ?>
        <div class="form-field">
            <label for="ns-vr-color"><?php _e( 'رنگ', 'nias-variation-swatches' ); ?></label>
            <input type="text" name="ns_vr_color" id="ns-vr-color" class="ns-vr-color-picker" value="#ffffff">
            <p class="description"><?php _e( 'یک رنگ برای این مقدار ویژگی انتخاب کنید.', 'nias-variation-swatches' ); ?></p>
        </div>
        <?php
    }

    public function edit_color_picker_field( $term, $taxonomy ) {
        $color = get_term_meta( $term->term_id, 'ns_vr_color', true );
        $color = ! empty( $color ) ? esc_attr( $color ) : '#ffffff';
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="ns-vr-color"><?php _e( 'رنگ', 'nias-variation-swatches' ); ?></label></th>
            <td>
                <input type="text" name="ns_vr_color" id="ns-vr-color" class="ns-vr-color-picker" value="<?php echo $color; ?>">
                <p class="description"><?php _e( 'یک رنگ برای این مقدار ویژگی انتخاب کنید.', 'nias-variation-swatches' ); ?></p>
            </td>
        </tr>
        <?php
    }

    public function save_color_picker_field( $term_id ) {
        if ( isset( $_POST['ns_vr_color'] ) && ! empty( $_POST['ns_vr_color'] ) ) {
            $color = sanitize_hex_color( $_POST['ns_vr_color'] );
            update_term_meta( $term_id, 'ns_vr_color', $color );
        } else {
            delete_term_meta( $term_id, 'ns_vr_color' );
        }
    }
}
