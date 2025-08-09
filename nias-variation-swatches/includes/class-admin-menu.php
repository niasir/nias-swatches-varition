<?php
/**
 * Admin Menu and Settings Page Management
 *
 * @package Nias_Variation_Swatches
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class NS_VR_Admin_Menu {
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    public function admin_menu() {
        add_menu_page(
            __( 'تنظیمات متغیر پیشرفته نیاس', 'nias-variation-swatches' ),
            __( 'متغیر پیشرفته نیاس', 'nias-variation-swatches' ),
            'manage_options',
            'nias-variation-swatches-settings',
            array( $this, 'settings_page' ),
            'dashicons-admin-settings',
            58
        );
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'ns_vr_settings_group' );
                do_settings_sections( 'nias-variation-swatches-settings' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function register_settings() {
        register_setting( 'ns_vr_settings_group', 'ns_vr_settings', array( 'sanitize_callback' => array( $this, 'sanitize_settings' ) ) );

        // General Section
        add_settings_section(
            'ns_vr_general_section',
            __( 'تنظیمات عمومی', 'nias-variation-swatches' ),
            null,
            'nias-variation-swatches-settings'
        );

        add_settings_field(
            'ns_vr_default_type',
            __( 'نوع نمایش پیشفرض ویژگی', 'nias-variation-swatches' ),
            array( $this, 'render_default_type_field' ),
            'nias-variation-swatches-settings',
            'ns_vr_general_section'
        );

        // Style Section
        add_settings_section(
            'ns_vr_style_section',
            __( 'تنظیمات استایل', 'nias-variation-swatches' ),
            null,
            'nias-variation-swatches-settings'
        );

        add_settings_field(
            'ns_vr_swatch_style',
            __( 'استایل نمونه رنگ', 'nias-variation-swatches' ),
            array( $this, 'render_swatch_style_field' ),
            'nias-variation-swatches-settings',
            'ns_vr_style_section'
        );
    }

    public function render_default_type_field() {
        $options = get_option( 'ns_vr_settings' );
        $value = isset( $options['default_type'] ) ? $options['default_type'] : 'button';
        ?>
        <label>
            <input type="radio" name="ns_vr_settings[default_type]" value="color" <?php checked( $value, 'color' ); ?>>
            <?php _e( 'نمونه رنگ', 'nias-variation-swatches' ); ?>
        </label>
        <br>
        <label>
            <input type="radio" name="ns_vr_settings[default_type]" value="button" <?php checked( $value, 'button' ); ?>>
            <?php _e( 'دکمه', 'nias-variation-swatches' ); ?>
        </label>
        <?php
    }

    public function render_swatch_style_field() {
        $options = get_option( 'ns_vr_settings' );
        $width = isset( $options['swatch_width'] ) ? $options['swatch_width'] : '32';
        $height = isset( $options['swatch_height'] ) ? $options['swatch_height'] : '32';
        $shape = isset( $options['swatch_shape'] ) ? $options['swatch_shape'] : 'circle';
        ?>
        <p>
            <label for="ns_vr_settings[swatch_width]"><?php _e( 'عرض:', 'nias-variation-swatches' ); ?></label>
            <input type="number" name="ns_vr_settings[swatch_width]" id="ns_vr_settings[swatch_width]" value="<?php echo esc_attr( $width ); ?>" class="small-text"> px
        </p>
        <p>
            <label for="ns_vr_settings[swatch_height]"><?php _e( 'ارتفاع:', 'nias-variation-swatches' ); ?></label>
            <input type="number" name="ns_vr_settings[swatch_height]" id="ns_vr_settings[swatch_height]" value="<?php echo esc_attr( $height ); ?>" class="small-text"> px
        </p>
        <p>
            <label><?php _e( 'شکل:', 'nias-variation-swatches' ); ?></label>
            <br>
            <label>
                <input type="radio" name="ns_vr_settings[swatch_shape]" value="circle" <?php checked( $shape, 'circle' ); ?>>
                <?php _e( 'دایره', 'nias-variation-swatches' ); ?>
            </label>
            <br>
            <label>
                <input type="radio" name="ns_vr_settings[swatch_shape]" value="square" <?php checked( $shape, 'square' ); ?>>
                <?php _e( 'مربع', 'nias-variation-swatches' ); ?>
            </label>
        </p>
        <?php
    }

    public function sanitize_settings( $input ) {
        $new_input = array();

        if ( isset( $input['default_type'] ) ) {
            $new_input['default_type'] = sanitize_text_field( $input['default_type'] );
        }
        if ( isset( $input['swatch_width'] ) ) {
            $new_input['swatch_width'] = absint( $input['swatch_width'] );
        }
        if ( isset( $input['swatch_height'] ) ) {
            $new_input['swatch_height'] = absint( $input['swatch_height'] );
        }
        if ( isset( $input['swatch_shape'] ) ) {
            $new_input['swatch_shape'] = sanitize_text_field( $input['swatch_shape'] );
        }

        return $new_input;
    }
}
