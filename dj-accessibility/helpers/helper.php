<?php
/**
 * @package DJ-Accessibility
 * @copyright Copyright (C) DJ-Extensions.com, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email faktycznie@gmail.com
 */

use YOOtheme\Application;
use function YOOtheme\app;

class DJAcc {

	public static function getName() {
		$plugin_type = self::pluginType();
		$subtitle = ( $plugin_type ) ? 'Pro' : 'Light';
		$subtitle .= ( !$plugin_type ) ? ' <a class="button button-secondary" href="https://dj-extensions.com/yootheme/dj-accessibility">Get Pro</a>' : '';
		return esc_html__( 'DJ-Accessibility', 'dj-accessibility' ) . ' ' . $subtitle;
	}

	public static function checkTheme() {
		$t = wp_get_theme();
		$parent = ( !empty( $t->parent() ) ) ? $t->parent() : false;
		$name = ( $parent ) ? $parent->get('Name') : $t->get('Name');
		return strtolower($name);
	}

	public static function checkYootheme() {
		return ( 'yootheme' === self::checkTheme() ) ? true : false;
	}

	public static function getOption( $key = '', $default = false ) {
		if ( function_exists( 'cmb2_get_option' ) ) {
			// Use cmb2_get_option as it passes through some key filters.
			return cmb2_get_option( 'djacc_options', $key, $default );
		}
	
		// Fallback to get_option if CMB2 is not loaded yet.
		$opts = get_option( 'djacc_options', $default );
	
		$val = $default;
	
		if ( 'all' == $key ) {
			$val = $opts;
		} elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
			$val = $opts[ $key ];
		}
	
		return $val;
	}

	public static function getParam( $param, $default = null ) {
		$param = str_replace('djacc_', '', $param);
		
		$field_wp = self::getOption('djacc_' . $param, $default);

		//fields priority: Yootheme > WP > default

		if( DJACC_YOOTHEME ) {
			return app()->config->get('~theme.djacc_' . $param, $field_wp);
		} else {
			return $field_wp;
		}
	}

	public static function getLayout( $layout ) {

		if( 'toolbar' == $layout ) {
			$template = 'toolbar.php';
		} else {
			$template = 'default.php';
		}

		if ( $overridden_template = locate_template( 'djacc/' . $template ) ) {
			load_template( $overridden_template );
		} else {
			load_template( DJACC_PATH . '/tmpl/' . $template );
		}
	}

	public static function pluginType() {
		return ( class_exists('DJAccPro') ) ? DJAccPro::getVersion() : false;
	}

	public static function saveDID($dlid) {
		$opt = get_option( 'djacc_options' );
		$opt['djacc_dlid'] = array('key' => $dlid);
		update_option( 'djacc_options', $opt );
	}

	public static function getDID() {
		$opt = get_option( 'djacc_options' );
		return ( !empty($opt['djacc_dlid']) ) ? $opt['djacc_dlid']['key'] : false;
	}
}

?>