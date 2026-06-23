<?php
/*
Plugin Name: Button for Doctolib
Description: Add a floating button for Doctolib
Author: Clément MARTINEZ
Author URI: https://clementmartinez.fr/
Text Domain: doctolib-btn
Domain Path: /languages/
Version: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
defined( 'ABSPATH' ) or die( '!' );

define( 'DOCTOLIB_BTN_VERSION', '1.0' );

$dummy_name = __( 'Button for Doctolib', 'doctolib-btn' );
$dummy_desc = __( 'Add a floating button for Doctolib', 'doctolib-btn' );

function doctolib_btn_lang() {
    load_plugin_textdomain( 'doctolib-btn', FALSE, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action('init', 'doctolib_btn_lang');


class DoctolibSettings
{
    private $options;

    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_color_picker' ) );
    }

    public function enqueue_color_picker( $hook ) {
        if ( 'settings_page_doctolib-btn-settings' !== $hook ) return;
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'doctolib-btn-admin', plugin_dir_url( __FILE__ ) . 'app.js', array( 'wp-color-picker' ), DOCTOLIB_BTN_VERSION, true );
    }

    public function add_plugin_page()
    {
        add_options_page(
            __('Doctolib Settings' ,'doctolib-btn'),
            __('Doctolib Settings' ,'doctolib-btn'),
            'manage_options',
            'doctolib-btn-settings',
            array( $this, 'create_admin_page' )
        );
    }

    public function create_admin_page()
    {
        $this->options = get_option( 'doctolib_btn_option' );
        ?>
        <div class="wrap">
            <form method="post" action="options.php">
            <?php
                settings_fields( 'doctolib_btn_option_group' );
                do_settings_sections( 'doctolib-btn-settings' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    public function page_init()
    {
        register_setting(
            'doctolib_btn_option_group',
            'doctolib_btn_option',
            array( $this, 'sanitize' )
        );

        add_settings_section(
            'doctolib_btn_setting_section',
            __('Doctolib Settings' ,'doctolib-btn'),
            array( $this, 'print_section_info' ),
            'doctolib-btn-settings'
        );

        add_settings_field(
            'doctolib_btn_title',
            __('Label' ,'doctolib-btn'),
            array( $this, 'title_callback' ),
            'doctolib-btn-settings',
            'doctolib_btn_setting_section'
        );

        add_settings_field(
            'doctolib_btn_link',
            __('Link' ,'doctolib-btn'),
            array( $this, 'link_callback' ),
            'doctolib-btn-settings',
            'doctolib_btn_setting_section'
        );

        add_settings_field(
            'doctolib_btn_color',
            __('Color' ,'doctolib-btn'),
            array( $this, 'color_callback' ),
            'doctolib-btn-settings',
            'doctolib_btn_setting_section'
        );

        add_settings_field(
            'doctolib_btn_position',
            __('Position' ,'doctolib-btn'),
            array( $this, 'position_callback' ),
            'doctolib-btn-settings',
            'doctolib_btn_setting_section'
        );

        add_settings_field(
            'doctolib_btn_target',
            __('Open in' ,'doctolib-btn'),
            array( $this, 'target_callback' ),
            'doctolib-btn-settings',
            'doctolib_btn_setting_section'
        );

        add_settings_field(
            'doctolib_btn_delay',
            __('Appearance delay (seconds)' ,'doctolib-btn'),
            array( $this, 'delay_callback' ),
            'doctolib-btn-settings',
            'doctolib_btn_setting_section'
        );

        add_settings_field(
            'doctolib_btn_show_icon',
            __('Show Doctolib icon' ,'doctolib-btn'),
            array( $this, 'show_icon_callback' ),
            'doctolib-btn-settings',
            'doctolib_btn_setting_section'
        );

        add_settings_field(
            'doctolib_btn_visibility',
            __('Visibility' ,'doctolib-btn'),
            array( $this, 'visibility_callback' ),
            'doctolib-btn-settings',
            'doctolib_btn_setting_section'
        );
    }

    public function sanitize( $input )
    {
        $new_input = array();
        $allowed_positions = array( 'left-top', 'right-top', 'left-bottom', 'right-bottom', 'large-bottom', 'large-top' );
        $allowed_visibility = array( 'all', 'home', 'not-home' );

        if ( isset( $input['doctolib_btn_title'] ) )
            $new_input['doctolib_btn_title'] = sanitize_text_field( $input['doctolib_btn_title'] );

        if ( isset( $input['doctolib_btn_link'] ) )
            $new_input['doctolib_btn_link'] = esc_url_raw( $input['doctolib_btn_link'] );

        if ( isset( $input['doctolib_btn_color'] ) && preg_match( '/^#[a-fA-F0-9]{6}$/', $input['doctolib_btn_color'] ) )
            $new_input['doctolib_btn_color'] = sanitize_hex_color( $input['doctolib_btn_color'] );
        else
            $new_input['doctolib_btn_color'] = '#0596DE';

        if ( isset( $input['doctolib_btn_position'] ) && in_array( $input['doctolib_btn_position'], $allowed_positions ) )
            $new_input['doctolib_btn_position'] = $input['doctolib_btn_position'];

        $new_input['doctolib_btn_target'] = isset( $input['doctolib_btn_target'] ) ? '1' : '0';

        if ( isset( $input['doctolib_btn_delay'] ) )
            $new_input['doctolib_btn_delay'] = absint( $input['doctolib_btn_delay'] );

        $new_input['doctolib_btn_show_icon'] = isset( $input['doctolib_btn_show_icon'] ) ? '1' : '0';

        if ( isset( $input['doctolib_btn_visibility'] ) && in_array( $input['doctolib_btn_visibility'], $allowed_visibility ) )
            $new_input['doctolib_btn_visibility'] = $input['doctolib_btn_visibility'];
        else
            $new_input['doctolib_btn_visibility'] = 'all';

        return $new_input;
    }

    public function print_section_info()
    {
        print __('Fill all the fields to make the button appear :)' ,'doctolib-btn');
    }

    public function title_callback()
    {
        printf(
            '<input class="regular-text" type="text" id="doctolib_btn_title" name="doctolib_btn_option[doctolib_btn_title]" value="%s" />',
            isset( $this->options['doctolib_btn_title'] ) ? esc_attr( $this->options['doctolib_btn_title'] ) : ''
        );
    }

    public function link_callback()
    {
        printf(
            '<input class="regular-text" type="url" id="doctolib_btn_link" name="doctolib_btn_option[doctolib_btn_link]" value="%s" />',
            isset( $this->options['doctolib_btn_link'] ) ? esc_url( $this->options['doctolib_btn_link'] ) : ''
        );
    }

    public function color_callback()
    {
        $color = isset( $this->options['doctolib_btn_color'] ) ? esc_attr( $this->options['doctolib_btn_color'] ) : '#0596DE';
        printf(
            '<input type="text" id="doctolib_btn_color" name="doctolib_btn_option[doctolib_btn_color]" value="%s" class="doctolib-color-picker" />',
            $color
        );
    }

    public function position_callback()
    {
        $selected = isset( $this->options['doctolib_btn_position'] ) ? (string) $this->options['doctolib_btn_position'] : 'left-top';
        $positions = array(
            'left-top'     => __('Left Top', 'doctolib-btn'),
            'right-top'    => __('Right Top', 'doctolib-btn'),
            'left-bottom'  => __('Left Bottom', 'doctolib-btn'),
            'right-bottom' => __('Right Bottom', 'doctolib-btn'),
            'large-bottom' => __('Full width - Bottom', 'doctolib-btn'),
            'large-top'    => __('Full width - Top', 'doctolib-btn'),
        );
        foreach ( $positions as $value => $label ) :
            printf(
                '<input type="radio" name="doctolib_btn_option[doctolib_btn_position]" id="%1$s" value="%1$s" %2$s> <label for="%1$s">%3$s</label><br/>',
                esc_attr( $value ),
                checked( $selected, $value, false ),
                esc_html( $label )
            );
        endforeach;
    }

    public function target_callback()
    {
        $checked = isset( $this->options['doctolib_btn_target'] ) ? (bool) $this->options['doctolib_btn_target'] : true;
        printf(
            '<input type="checkbox" id="doctolib_btn_target" name="doctolib_btn_option[doctolib_btn_target]" value="1" %s /> <label for="doctolib_btn_target">%s</label>',
            checked( $checked, true, false ),
            __('Open in a new tab', 'doctolib-btn')
        );
    }

    public function delay_callback()
    {
        $delay = isset( $this->options['doctolib_btn_delay'] ) ? absint( $this->options['doctolib_btn_delay'] ) : 0;
        printf(
            '<input type="number" min="0" max="30" id="doctolib_btn_delay" name="doctolib_btn_option[doctolib_btn_delay]" value="%d" /> <p class="description">%s</p>',
            $delay,
            __('0 = visible immediately. The button fades in after the defined delay.', 'doctolib-btn')
        );
    }

    public function show_icon_callback()
    {
        $checked = isset( $this->options['doctolib_btn_show_icon'] ) ? (bool) $this->options['doctolib_btn_show_icon'] : true;
        printf(
            '<input type="checkbox" id="doctolib_btn_show_icon" name="doctolib_btn_option[doctolib_btn_show_icon]" value="1" %s /> <label for="doctolib_btn_show_icon">%s</label>',
            checked( $checked, true, false ),
            __('Show the Doctolib logo next to the label', 'doctolib-btn')
        );
    }

    public function visibility_callback()
    {
        $selected = isset( $this->options['doctolib_btn_visibility'] ) ? $this->options['doctolib_btn_visibility'] : 'all';
        $options = array(
            'all'      => __('All pages', 'doctolib-btn'),
            'home'     => __('Home page only', 'doctolib-btn'),
            'not-home' => __('All pages except home', 'doctolib-btn'),
        );
        foreach ( $options as $value => $label ) :
            printf(
                '<input type="radio" name="doctolib_btn_option[doctolib_btn_visibility]" id="visibility_%1$s" value="%1$s" %2$s> <label for="visibility_%1$s">%3$s</label><br/>',
                esc_attr( $value ),
                checked( $selected, $value, false ),
                esc_html( $label )
            );
        endforeach;
    }
}

if ( is_admin() ):
    $my_settings_page = new DoctolibSettings();
endif;


function doctolib_btn_action_links( $links ) {
    $links = array_merge( array(
        '<a href="' . esc_url( admin_url( '/options-general.php?page=doctolib-btn-settings' ) ) . '">' . __( 'Settings', 'doctolib-btn' ) . '</a>'
    ), $links );
    return $links;
}
add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'doctolib_btn_action_links' );


function doctolib_btn_generate() {
    $options = get_option( 'doctolib_btn_option' );

    if ( ! is_array( $options ) ) return;
    if ( empty( $options['doctolib_btn_link'] ) || empty( $options['doctolib_btn_title'] ) || empty( $options['doctolib_btn_position'] ) ) return;

    $visibility = isset( $options['doctolib_btn_visibility'] ) ? $options['doctolib_btn_visibility'] : 'all';
    if ( $visibility === 'home' && ! is_front_page() ) return;
    if ( $visibility === 'not-home' && is_front_page() ) return;

    $color     = isset( $options['doctolib_btn_color'] ) ? esc_attr( $options['doctolib_btn_color'] ) : '#0596DE';
    $target    = isset( $options['doctolib_btn_target'] ) && $options['doctolib_btn_target'] ? '_blank' : '_self';
    $delay     = isset( $options['doctolib_btn_delay'] ) ? absint( $options['doctolib_btn_delay'] ) : 0;
    $show_icon = isset( $options['doctolib_btn_show_icon'] ) ? (bool) $options['doctolib_btn_show_icon'] : true;
    $position  = esc_attr( $options['doctolib_btn_position'] );
    $label     = esc_html( $options['doctolib_btn_title'] );
    $link      = esc_url( $options['doctolib_btn_link'] );

    $rel = ( $target === '_blank' ) ? 'nofollow noopener noreferrer' : 'nofollow';

    ?>
    <a href="<?php echo $link; ?>"
       rel="<?php echo $rel; ?>"
       target="<?php echo $target; ?>"
       class="doctolib_btn doctolib_btn_position_<?php echo $position; ?>"
       style="background-color:<?php echo $color; ?>;"
       aria-label="<?php echo $label; ?> - Doctolib"
       data-delay="<?php echo $delay; ?>">
        <span><?php echo $label; ?></span>
        <?php if ( $show_icon ) : ?>
            <img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'doctolib.png' ); ?>" alt="" aria-hidden="true">
        <?php endif; ?>
    </a>
    <?php
}
add_action( 'wp_footer', 'doctolib_btn_generate' );


function doctolib_btn_style() {
    wp_enqueue_style( 'doctolib-btn-style', plugin_dir_url( __FILE__ ) . 'style.css', array(), DOCTOLIB_BTN_VERSION );
    wp_enqueue_script( 'doctolib-btn-front', plugin_dir_url( __FILE__ ) . 'app.js', array(), DOCTOLIB_BTN_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'doctolib_btn_style' );