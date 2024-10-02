<?php
/**
 * @package DJ-Accessibility
 * @copyright Copyright (C) DJ-Extensions.com, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email faktycznie@gmail.com
 */

use YOOtheme\Application;

if ( !class_exists ( 'DJAccessibility' ) ) {

	require_once __DIR__ . '/helpers/helper.php';
	if( file_exists(__DIR__ . '/helpers/pro.php') ) require_once __DIR__ . '/helpers/pro.php';
	if( file_exists(__DIR__ . '/helpers/updates.php') ) require_once __DIR__ . '/helpers/updates.php';

	class DJAccessibility {
		function __construct() {
			add_action( 'plugins_loaded', array( $this, 'init' ) );
			add_action( 'after_setup_theme', array( $this, 'init_module' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_files' ) );
			add_action( 'wp_body_open', array( $this, 'prepend_body' ) );
			add_action( 'wp_footer', array( $this, 'prepend_body' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin' ) );
			add_action( 'wp_ajax_djacc_load_template', array($this, 'ajax_load_template') );
			add_action( 'wp_ajax_nopriv_djacc_load_template', array($this, 'ajax_load_template') );
		}

		public function init() {

			load_plugin_textdomain('dj-accessibility', false, dirname(plugin_basename(__FILE__)) . '/languages/');

			// check if YOOtheme Pro is loaded
			define('DJACC_YOOTHEME', DJAcc::checkYootheme());

			if( is_admin() ) {
				require __DIR__ . '/includes/cmb2/init.php';
				require __DIR__ . '/helpers/options.php';
			}
		}

		function init_module() {
			if( DJACC_YOOTHEME ) {
				/* Load Needed Classes */
				require __DIR__ . '/module/src/ConfigListener.php';
				
			
				// Load a single module from the same directory
				$app = Application::getInstance();
				$app->load(__DIR__ . '/module/bootstrap.php');
			}
		}

		function prepend_body() {
			if( is_admin() ) {
				return;
			}

			if ( doing_action( 'wp_body_open' ) ) {
				remove_action ( 'wp_footer', array( $this, 'prepend_body' ) );
			}

			//load template
			$layout = DJAcc::getParam('layout', 'popup');
			DJAcc::getLayout($layout);
		}

		function enqueue_files() {

			$min = ( DJACC_DEBUG ) ? '' : '.min';
			$ver = ( DJACC_DEBUG ) ? DJACC_VERSION . '-' . time() : DJACC_VERSION;
	
			wp_enqueue_style('djacc-style', plugin_dir_url( __FILE__ ) . 'module/assets/css/accessibility.css', array(), $ver );
			wp_enqueue_script('djacc-script', plugin_dir_url( __FILE__ ) . 'module/assets/js/accessibility' . $min . '.js', array(), $ver, true);
		
			$load_font = DJAcc::getParam('djacc_load_font', 1);
			$font_url = DJAcc::getParam('font_url', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');
			$font_family = DJAcc::getParam('font_family', 'Roboto, sans-serif');
	
			if( !empty($load_font) ) {
				if( !empty($font_url) ) {
					$font_url = filter_var($font_url, FILTER_SANITIZE_URL);
					wp_enqueue_style('djacc-webfont', $font_url, array(), $ver );
				}
				if( !empty($font_family) ) {
					$font_family = htmlspecialchars(trim($font_family, ';'));
					wp_add_inline_style('djacc-style', '.djacc { font-family: '.esc_attr($font_family).'; }');
				}
			}

			//inline css styles
			$position = DJAcc::getParam('position', 'sticky');
			$mobile_position = DJAcc::getParam('mobile_position', 'sticky');
			$layout = DJAcc::getParam('layout', 'popup');
			$mobile_layout = DJAcc::getParam('mobile_layout', 'popup');

			$popup_voff = DJAcc::getParam('voff_popup', 20);
			$popup_hoff = DJAcc::getParam('hoff_popup', 20);

			if( $popup_voff > 0 || $popup_hoff > 0 ) wp_add_inline_style('djacc-style', '.djacc--sticky.djacc-popup { margin: '.esc_attr($popup_voff).'px '.esc_attr($popup_hoff).'px; }');
			
			$toolbar_voff = DJAcc::getParam('voff_toolbar', 0);
			$toolbar_hoff = DJAcc::getParam('hoff_toolbar', 0);

			if( $toolbar_voff > 0 || $toolbar_hoff > 0 ) wp_add_inline_style('djacc-style', '.djacc--sticky.djacc-toolbar { margin: '.esc_attr($toolbar_voff).'px '.esc_attr($toolbar_hoff).'px; }');

			$align_popup = DJAcc::getParam('align_popup', 'top right');

			$btn = DJAcc::getParam('image', false);
			$width = DJAcc::getParam('width', 48);
			$height = DJAcc::getParam('height', 48);

			if( $btn ) wp_add_inline_style('djacc-style', '.djacc-popup .djacc__openbtn { width: '.esc_attr($width).'px; height: '.esc_attr($height).'px; }');

			$align_toolbar = DJAcc::getParam('align_toolbar', 'top center');

			$align_mobile_position  = DJAcc::getParam('align_mobile', 'bottom right');
			$direction = DJAcc::getParam('direction', 'top left');
			$space = DJAcc::getParam('space', true);

			$speech_pitch = DJAcc::getParam('speech_pitch', '1');
			$speech_rate = DJAcc::getParam('speech_rate', '1');
			$speech_volume = DJAcc::getParam('speech_volume', '1');

			$plugin_type = DJAcc::pluginType();

			$options = json_encode(array(
				'cms'                    => 'wp',
				'yootheme'               => DJACC_YOOTHEME,
				'position'               => esc_js($position),
				'mobile_position'        => esc_js($mobile_position),
				'layout'                 => esc_js($layout),
				'mobile_layout'          => esc_js($mobile_layout),
				'align_position_popup'   => esc_js($align_popup),
				'align_position_toolbar' => esc_js($align_toolbar),
				'align_mobile_position'  => esc_js($align_mobile_position),
				'breakpoint'             => '767px',
				'direction'              => esc_js($direction),
				'space'                  => esc_js($space),
				'version'                => esc_js($plugin_type),
				'speech_pitch'           => $speech_pitch,
				'speech_rate'            => $speech_rate,
				'speech_volume'          => $speech_volume,
				'ajax_url'               => admin_url( 'admin-ajax.php' ),
				'ajax_action'            => 'djacc_load_template',
			));

			// init script
			$js = 'new DJAccessibility(' . $options . ')';
			wp_add_inline_script('djacc-script', $js);

		}

		function ajax_load_template() {
			$layout = (isset($_REQUEST['djacc_template'])) ? $_REQUEST['djacc_template'] : false;
			if($layout) {
				DJAcc::getLayout($layout);
			}
			wp_die();
		}

		function enqueue_admin( $hook ) {
			
			if ( 'toplevel_page_djacc_options' != $hook ) {
				return;
			}
			wp_enqueue_style('djacc-admin-style', plugin_dir_url( __FILE__ ) . 'assets/djacc-admin.css', array(), DJACC_VERSION);
			wp_enqueue_script('cmb2-dj-conditional', plugin_dir_url( __FILE__ ) . 'assets/cmb2-dj-conditional.js', array('jquery'), DJACC_VERSION, true);
		
		}
	}
	new DJAccessibility();
}
?>