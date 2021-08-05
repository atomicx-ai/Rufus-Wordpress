<?php
/**
 * Plugin Name: Rufus WP 
 * Plugin URI: https://github.com/atomicx-ai/rufus-wp
 * Description: Wordpress Rufus integration
 * Version: 1.0
 * Author: Atomic X 
 * Author URI: https://atomicx.ai/
 **/

class RufusWordpress {
	private $rufus_wordpress_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'rufus_wordpress_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'rufus_wordpress_page_init' ) );
	}

	public function rufus_wordpress_add_plugin_page() {
		add_options_page(
			'Rufus Wordpress', // page_title
			'Rufus Wordpress', // menu_title
			'manage_options', // capability
			'rufus-wordpress', // menu_slug
			array( $this, 'rufus_wordpress_create_admin_page' ) // function
		);
	}

	public function rufus_wordpress_create_admin_page() {
		$this->rufus_wordpress_options = get_option( 'rufus_wordpress_option_name' ); ?>

		<div class="wrap">
			<h2>Rufus Wordpress</h2>
			<p></p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'rufus_wordpress_option_group' );
					do_settings_sections( 'rufus-wordpress-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function rufus_wordpress_page_init() {
		register_setting(
			'rufus_wordpress_option_group', // option_group
			'rufus_wordpress_option_name', // option_name
			array( $this, 'rufus_wordpress_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'rufus_wordpress_setting_section', // id
			'Settings', // title
			array( $this, 'rufus_wordpress_section_info' ), // callback
			'rufus-wordpress-admin' // page
		);

		add_settings_field(
			'key_0', // id
			'Key', // title
			array( $this, 'key_0_callback' ), // callback
			'rufus-wordpress-admin', // page
			'rufus_wordpress_setting_section' // section
		);

		add_settings_field(
			'css_2', // id
			'CSS', // title
			array( $this, 'css_2_callback' ), // callback
			'rufus-wordpress-admin', // page
			'rufus_wordpress_setting_section' // section
		);
	}

	public function rufus_wordpress_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['key_0'] ) ) {
			$sanitary_values['key_0'] = sanitize_text_field( $input['key_0'] );
		}

		if ( isset( $input['css_2'] ) ) {
			$sanitary_values['css_2'] = esc_textarea( $input['css_2'] );
		}

		return $sanitary_values;
	}

	public function rufus_wordpress_section_info() {
		
	}

	public function key_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="rufus_wordpress_option_name[key_0]" id="key_0" value="%s">',
			isset( $this->rufus_wordpress_options['key_0'] ) ? esc_attr( $this->rufus_wordpress_options['key_0']) : ''
		);
	}

	public function css_2_callback() {
		printf(
			'<textarea class="large-text" rows="5" name="rufus_wordpress_option_name[css_2]" id="css_2">%s</textarea>',
			isset( $this->rufus_wordpress_options['css_2'] ) ? esc_attr( $this->rufus_wordpress_options['css_2']) : ''
		);
	}

}
if ( is_admin() )
	$rufus_wordpress = new RufusWordpress();

/* 
 * Retrieve this value with:
 * $rufus_wordpress_options = get_option( 'rufus_wordpress_option_name' ); // Array of All Options
 * $key_0 = $rufus_wordpress_options['key_0']; // Key
 * $css_2 = $rufus_wordpress_options['css_2']; // CSS
 */

add_action( 'wp_enqueue_scripts', 'rufusScripts' );
function rufusScripts() {
    wp_enqueue_script( 'velocity', 'https://cdnjs.cloudflare.com/ajax/libs/velocity/1.5.1/velocity.min.js', array('jquery'), '1.0.0', true );
    wp_enqueue_script( 'sockets', 'https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.2.0/socket.io.js', array('jquery'), '1.0.0', true );
    wp_enqueue_script( 'cookie', 'https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js', array('jquery'), '1.0.0', true );
    wp_enqueue_script( 'materialize', 'https://dashboard.atomicx1.com/css/embed/materialize.js', array('jquery'), '1.0.0', true );
}

add_action('wp_footer', 'rufusSetup');
function rufusSetup() {
    
$rufus_wordpress_options = get_option( 'rufus_wordpress_option_name' );
$key_0 = $rufus_wordpress_options['key_0']; // Key
$css_2 = $rufus_wordpress_options['css_2']; // CSS

$rufusString = <<<"rufusString"
    <script>
        setTimeout(function() {
            $ = jQuery.noConflict(true);
            $.getScript("https://dashboard.atomicx1.com/embed", function(data, textStatus, jqxhr) {});
        }, 2000)
        var rufuskey = '$key_0';
        var rufuslanding = false;
        var socketEndpoint = "//dashboard.atomicx1.com"
    </script>
    
    <link rel="stylesheet" type="text/css" href="https://dashboard.atomicx1.com/css/embed/embed.css">
    <style>
        $css_2
    </style>
rufusString;

    echo $rufusString;
    }