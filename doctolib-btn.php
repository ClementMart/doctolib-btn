<?php
/*
Plugin Name: Button for Doctolib
Description: Add a floating button for Doctolib
Author: Clément MARTINEZ
Author URI: https://clementmartinez.fr/
Text Domain: doctolib-btn
Domain Path: /languages/
Version: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
*** Languages
**/
$dummy_name = __( 'Button for Doctolib', 'doctolib-btn' );
$dummy_desc = __( 'Add a floating button for Doctolib', 'doctolib-btn' );
function doctolib_btn_lang() {
    load_plugin_textdomain( 'doctolib-btn', FALSE, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action('init', 'doctolib_btn_lang');

/**
*** Add setting page
**/
class DoctolibSettings
{
    private $options;

    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
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
            'doctolib_btn_position', 
            __('Position' ,'doctolib-btn'), 
            array( $this, 'position_callback' ), 
            'doctolib-btn-settings', 
            'doctolib_btn_setting_section'
        );
    }

    public function sanitize( $input )
    {
        $new_input = array();

        if( isset( $input['doctolib_btn_title'] ) ):
            $new_input['doctolib_btn_title'] = sanitize_text_field( $input['doctolib_btn_title'] );
        endif;

        if( isset( $input['doctolib_btn_link'] ) ):
            $new_input['doctolib_btn_link'] = esc_url_raw( $input['doctolib_btn_link'] );
        endif;

        if( isset( $input['doctolib_btn_position'] ) ):
            $new_input['doctolib_btn_position'] = $input['doctolib_btn_position'];
        endif;

        return $new_input;
    }

    public function print_section_info()
    {
        print __('Fill all the field to make the button appear :)' ,'doctolib-btn');
    }
    
    public function title_callback()
    {
        printf(
            '<input class="regular-text" type="text" id="doctolib_btn_title" name="doctolib_btn_option[doctolib_btn_title]" value="%s" />',
            isset( $this->options['doctolib_btn_title'] ) ? esc_attr( $this->options['doctolib_btn_title']) : ''
        );
    }

    public function link_callback()
    {
        printf(
            '<input class="regular-text" type="url" id="doctolib_btn_link" name="doctolib_btn_option[doctolib_btn_link]" value="%s" />',
            isset( $this->options['doctolib_btn_link'] ) ? esc_url_raw( $this->options['doctolib_btn_link']) : ''
        );
    }

    public function position_callback()
    {
    	if(isset( $this->options['doctolib_btn_position'] )):
    		$selected = (string)$this->options['doctolib_btn_position'];
		else:
			$selected = false;
		endif;
        ?>

        <input type="radio" name="doctolib_btn_option[doctolib_btn_position]" id="left-top" value="left-top" <?php if(!$selected || $selected == 'left-top'): echo 'checked'; endif; ?>>
        <label for="left-top"><?php _e('Left Top' ,'doctolib-btn'); ?></label><br/>

        <input type="radio" name="doctolib_btn_option[doctolib_btn_position]" id="right-top" value="right-top" <?php if($selected == 'right-top'): echo 'checked'; endif; ?>>
        <label for="right-top"><?php _e('Right Top' ,'doctolib-btn'); ?></label><br/>

        <input type="radio" name="doctolib_btn_option[doctolib_btn_position]" id="left-bottom" value="left-bottom" <?php if($selected == 'left-bottom'): echo 'checked'; endif; ?>>
        <label for="left-bottom"><?php _e('Left Bottom' ,'doctolib-btn'); ?></label><br/>

        <input type="radio" name="doctolib_btn_option[doctolib_btn_position]" id="right-bottom" value="right-bottom" <?php if($selected == 'right-bottom'): echo 'checked'; endif; ?>>
        <label for="right-bottom"><?php _e('Right Bottom' ,'doctolib-btn'); ?></label><br/>

        <input type="radio" name="doctolib_btn_option[doctolib_btn_position]" id="large-bottom" value="large-bottom" <?php if($selected == 'large-bottom'): echo 'checked'; endif; ?>>
        <label for="large-bottom"><?php _e('Full width - Bottom' ,'doctolib-btn'); ?></label><br/>

        <input type="radio" name="doctolib_btn_option[doctolib_btn_position]" id="large-top" value="large-top" <?php if($selected == 'large-top'): echo 'checked'; endif; ?>>
        <label for="large-top"><?php _e('Full width - Top' ,'doctolib-btn'); ?></label>

        <?php
    }
}

if( is_admin() ):
    $my_settings_page = new DoctolibSettings();
endif;

/**
*** Add action link in plugin
**/
function doctolib_btn_action_links( $links ) {
	$links = array_merge( array(
		'<a href="' . esc_url( admin_url( '/options-general.php?page=doctolib-btn-settings' ) ) . '">' . __( 'Settings', 'doctolib-btn' ) . '</a>'
	), $links );
	return $links;
}
add_action('plugin_action_links_'.plugin_basename( __FILE__ ), 'doctolib_btn_action_links');


/**
***	Add HTML
**/
function doctolib_btn_generate() {
	$options = get_option('doctolib_btn_option');

	if($options['doctolib_btn_link'] && $options['doctolib_btn_title'] && $options['doctolib_btn_position']):
    ?>
    	<a href="<?php echo esc_url_raw($options['doctolib_btn_link']); ?>" rel="nofollow,noindex" target="_blank" class="doctolib_btn doctolib_btn_position_<?php echo esc_attr($options['doctolib_btn_position']); ?>"><span><?php echo esc_html($options['doctolib_btn_title']); ?></span><img src="<?php echo plugin_dir_url( __FILE__ ); ?>doctolib.png" alt="Doctolib"></a>
    <?php
	endif;
}
add_action( 'wp_footer', 'doctolib_btn_generate' );

/**
***	Includes style
**/
function doctolib_btn_style() {
    $plugin_url = plugin_dir_url( __FILE__ );
    wp_enqueue_style( 'doctolib-btn-style', $plugin_url . 'style.css' );
}
add_action( 'wp_enqueue_scripts', 'doctolib_btn_style' );