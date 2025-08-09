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
        // Hooks for attribute terms (color picker)
        $attribute_taxonomies = wc_get_attribute_taxonomies();
        if ( ! empty( $attribute_taxonomies ) ) {
            foreach ( $attribute_taxonomies as $tax ) {
                $taxonomy_name = wc_attribute_taxonomy_name( $tax->attribute_name );
                if ( $tax->attribute_type === 'select' ) {
                    add_action( "{$taxonomy_name}_add_form_fields", array( $this, 'add_color_picker_field' ) );
                    add_action( "{$taxonomy_name}_edit_form_fields", array( $this, 'edit_color_picker_field' ), 10, 2 );

                    add_action( "created_{$taxonomy_name}", array( $this, 'save_color_picker_field' ), 10, 2 );
                    add_action( "edited_{$taxonomy_name}", array( $this, 'save_color_picker_field' ), 10, 2 );
                }
            }
        }

        // Hooks for attribute settings (display type)
        add_action( 'woocommerce_after_add_attribute_fields', array( $this, 'add_attribute_display_type_field' ) );
        add_action( 'woocommerce_after_edit_attribute_fields', array( $this, 'edit_attribute_display_type_field' ) );

        // Hooks for saving attribute display type
        add_action( 'woocommerce_attribute_added', array( $this, 'save_attribute_display_type' ), 10, 2 );
        add_action( 'woocommerce_attribute_updated', array( $this, 'save_attribute_display_type' ), 10, 2 );

        // Enqueue scripts
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

    /**
     * Add display type field to the "Add Attribute" form.
     */
    public function add_attribute_display_type_field() {
        ?>
        <div class="form-field">
            <label for="ns_vr_attribute_display_type"><?php _e( 'نوع نمایش', 'nias-variation-swatches' ); ?></label>
            <select name="ns_vr_attribute_display_type" id="ns_vr_attribute_display_type">
                <option value="default"><?php _e( 'پیشفرض', 'nias-variation-swatches' ); ?></option>
                <option value="color"><?php _e( 'نمونه رنگ', 'nias-variation-swatches' ); ?></option>
                <option value="button"><?php _e( 'دکمه', 'nias-variation-swatches' ); ?></option>
            </select>
            <p class="description"><?php _e( 'نحوه نمایش این ویژگی در صفحه محصول را انتخاب کنید.', 'nias-variation-swatches' ); ?></p>
        </div>
        <?php
    }

    /**
     * Add display type field to the "Edit Attribute" form.
     */
    public function edit_attribute_display_type_field() {
        $attribute_id = $_GET['edit'];
        $display_type = get_option( 'ns_vr_attribute_display_type_' . $attribute_id );
        ?>
        <tr class="form-field">
            <th scope="row" valign="top">
                <label for="ns_vr_attribute_display_type"><?php _e( 'نوع نمایش', 'nias-variation-swatches' ); ?></label>
            </th>
            <td>
                <select name="ns_vr_attribute_display_type" id="ns_vr_attribute_display_type">
                    <option value="default" <?php selected( $display_type, 'default' ); ?>><?php _e( 'پیشفرض', 'nias-variation-swatches' ); ?></option>
                    <option value="color" <?php selected( $display_type, 'color' ); ?>><?php _e( 'نمونه رنگ', 'nias-variation-swatches' ); ?></option>
                    <option value="button" <?php selected( $display_type, 'button' ); ?>><?php _e( 'دکمه', 'nias-variation-swatches' ); ?></option>
                </select>
                <p class="description"><?php _e( 'نحوه نمایش این ویژگی در صفحه محصول را انتخاب کنید.', 'nias-variation-swatches' ); ?></p>
            </td>
        </tr>
        <?php
    }

    /**
     * Save the attribute display type.
     *
     * @param int   $attribute_id   Attribute ID.
     * @param array $attribute      Attribute data.
     */
    public function save_attribute_display_type( $attribute_id, $attribute ) {
        if ( isset( $_POST['ns_vr_attribute_display_type'] ) ) {
            $display_type = sanitize_text_field( $_POST['ns_vr_attribute_display_type'] );
            update_option( 'ns_vr_attribute_display_type_' . $attribute_id, $display_type );
        }
    }
}
