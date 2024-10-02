<?php
/**
 * @package DJ-Accessibility
 * @copyright Copyright (C) DJ-Extensions.com, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email faktycznie@gmail.com
 */

if( file_exists( DJACC_PATH . '/includes/cmb2_license.php' ) ) require_once DJACC_PATH . '/includes/cmb2_license.php';

add_action( 'cmb2_admin_init', 'djacc_register_options_metabox' );
/**
 * Hook in and register a metabox to handle a plugin options page and adds a menu item.
 */
function djacc_register_options_metabox() {

	/**
	 * Registers options page menu item and form.
	 */

	$cmb = new_cmb2_box( array(
		'id'           => 'djacc_options',
		'title'        => DJAcc::getName(),
		'object_types' => array( 'options-page' ),
		'option_key'      => 'djacc_options', // The option key and admin menu page slug.
		'icon_url'        => 'dashicons-universal-access-alt', // Menu icon. Only applicable if 'parent_slug' is left empty.
		'menu_title'      => esc_html__( 'DJ-Accessibility', 'dj-accessibility' ), // Falls back to 'title' (above).
	) );

	$plugin_type = DJAcc::pluginType();

	if( $plugin_type ) {
		$cmb->add_field( array(
			'name'             => esc_html__( 'License key', 'dj-accessibility' ),
			'desc'             => '',
			'id'               => 'djacc_dlid',
			'type'             => 'license',
		) );
	}

	if( DJACC_YOOTHEME ) {

		$cmb->add_field( array(
			'name' => '',
			'desc' => '<a class="button button-secondary" href="' . admin_url('customize.php?theme=yootheme') . '">' . esc_html__( 'Open Yootheme Accessibility options', 'dj-accessibility' ) . '</a>',
			'type' => 'title',
			'id'   => 'djacc_yootheme_button'
		) );

		return;
	}

	$cmb->add_field( array(
		'name'             => esc_html__('Theme', 'dj-accessibility'),
		'id'               => 'djacc_style',
		'type'             => 'select',
		'default'          => 'dark',
		'options'          => array(
			'dark'  => esc_html__( 'Dark', 'dj-accessibility' ),
			'light' => esc_html__( 'Light', 'dj-accessibility' ),
		),
	) );

	$cmb->add_field( array(
		'name'             => esc_html__('Type', 'dj-accessibility'),
		'desc'             => esc_html__('Type sticky means that the panel stays in the same place even if the page is scrolled. Custom position requires DJ-Accessibility widget or custom HTML code on the page:', 'dj-accessibility') . '<br><i>&lt;div id="djacc"&gt;&lt;/div&gt;</i>',
		'id'               => 'djacc_position',
		'type'             => 'select',
		'default'          => 'sticky',
		'options'          => array(
			'sticky' => esc_html__( 'Sticky (fixed)', 'dj-accessibility' ),
			'custom' => esc_html__( 'Static (custom element)', 'dj-accessibility' ),
		),
	) );

	$cmb->add_field( array(
		'name'             => esc_html__('Mobile type', 'dj-accessibility'),
		'id'               => 'djacc_mobile_position',
		'type'             => 'select',
		'default'          => 'sticky',
		'options'          => array(
			'sticky' => esc_html__( 'Sticky (fixed)', 'dj-accessibility' ),
			'custom' => esc_html__( 'Static (custom element)', 'dj-accessibility' ),
		),
	) );

	if( $plugin_type ) {
		$layouts = array(
			'popup'   => esc_html__( 'Popup', 'dj-accessibility' ),
			'toolbar' => esc_html__( 'Toolbar', 'dj-accessibility' ),
		);
	} else {
		$layouts = array(
			'popup'   => esc_html__( 'Popup', 'dj-accessibility' ),
		);
	}

	$cmb->add_field( array(
		'name'             => esc_html__('Layout', 'dj-accessibility'),
		'id'               => 'djacc_layout',
		'type'             => 'select',
		'default'          => 'popup',
		'options'          => $layouts,
	) );

	$cmb->add_field( array(
		'name'             => esc_html__('Mobile layout', 'dj-accessibility'),
		'id'               => 'djacc_mobile_layout',
		'type'             => 'select',
		'default'          => 'popup',
		'options'          => $layouts,
	) );

	$cmb->add_field( array(
		'name'             => esc_html__('[Popup] Open direction', 'dj-accessibility'),
		'desc'             => esc_html__('Choose the direction where the popup will open.', 'dj-accessibility'),
		'id'               => 'djacc_direction',
		'type'             => 'select',
		'default'          => 'top left',
		'options'          => array(
			'top center'    => esc_html__( 'down', 'dj-accessibility' ),
			'top right'     => esc_html__( 'down left', 'dj-accessibility' ),
			'top left'      => esc_html__( 'down right', 'dj-accessibility' ),
			'center left'   => esc_html__( 'left', 'dj-accessibility' ),
			'center right'  => esc_html__( 'right', 'dj-accessibility' ),
			'bottom center' => esc_html__( 'up', 'dj-accessibility' ),
			'bottom right'  => esc_html__( 'up left', 'dj-accessibility' ),
			'bottom left'   => esc_html__( 'up right', 'dj-accessibility' ),
		),
		'attributes'    => array(
			'data-conditional'     => wp_json_encode(array(
				'operator' => 'OR',
				'djacc_layout'=>'popup',
				'djacc_mobile_layout'=> 'popup',
			)),
		),
	) );

	$cmb->add_field( array(
		'name'             => esc_html__('[Popup] Position', 'dj-accessibility'),
		'id'               => 'djacc_align_popup',
		'type'             => 'select',
		'default'          => 'top right',
		'options'          => array(
			'top center'    => esc_html__( 'top', 'dj-accessibility' ),
			'top left'      => esc_html__( 'top left', 'dj-accessibility' ),
			'top right'     => esc_html__( 'top right', 'dj-accessibility' ),
			'center left'   => esc_html__( 'left', 'dj-accessibility' ),
			'center right'  => esc_html__( 'right', 'dj-accessibility' ),
			'bottom center' => esc_html__( 'bottom', 'dj-accessibility' ),
			'bottom left'   => esc_html__( 'bottom left', 'dj-accessibility' ),
			'bottom right'  => esc_html__( 'bottom right', 'dj-accessibility' ),
		),
		'attributes'    => array(
			'data-conditional'     => wp_json_encode(array(
				'operator' => 'OR',
				'djacc_layout'=>'popup',
				'djacc_mobile_layout'=> 'popup',
			)),
		),
	) );

	$cmb->add_field( array(
		'name'             => esc_html__('[Toolbar] Position', 'dj-accessibility'),
		'id'               => 'djacc_align_toolbar',
		'type'             => 'select',
		'default'          => 'top right',
		'options'          => array(
			'top center'    => esc_html__( 'top', 'dj-accessibility' ),
			'center left'   => esc_html__( 'left', 'dj-accessibility' ),
			'center right'  => esc_html__( 'right', 'dj-accessibility' ),
			'bottom center' => esc_html__( 'bottom', 'dj-accessibility' ),
		),
		'attributes'    => array(
			'data-conditional'     => wp_json_encode(array(
				'operator' => 'OR',
				'djacc_layout'=>'toolbar',
				'djacc_mobile_layout'=> 'toolbar',
			)),
		),
	) );


	$cmb->add_field( array(
		'name'             => esc_html__('Mobile position', 'dj-accessibility'),
		'desc'             => esc_html__('The panel position will be changed for mobile devices (< 767px)', 'dj-accessibility'),
		'id'               => 'djacc_align_mobile',
		'type'             => 'select',
		'default'          => 'bottom right',
		'options'          => array(
			'top center'    => esc_html__( 'top', 'dj-accessibility' ),
			'top left'      => esc_html__( 'top left', 'dj-accessibility' ),
			'top right'     => esc_html__( 'top right', 'dj-accessibility' ),
			'center left'   => esc_html__( 'left', 'dj-accessibility' ),
			'center right'  => esc_html__( 'right', 'dj-accessibility' ),
			'bottom center' => esc_html__( 'bottom', 'dj-accessibility' ),
			'bottom left'   => esc_html__( 'bottom left', 'dj-accessibility' ),
			'bottom right'  => esc_html__( 'bottom right', 'dj-accessibility' ),
		),
		'attributes'    => array(
			'data-conditional'     => wp_json_encode(array(
				'operator' => 'OR',
				'djacc_layout'=>'popup',
				'djacc_mobile_layout'=> 'popup',
			)),
		),
	) );

	$cmb->add_field( array(
		'name' => esc_html__('[Toolbar] Reserve space', 'dj-accessibility'),
		'desc' => esc_html__('Choose whether the toolbar should cover the page or not.', 'dj-accessibility'),
		'id'   => 'djacc_space',
		'type' => 'checkbox',
		'default'=> true,
		'sanitization_cb'  => 'djacc_sanitize_checkbox',
		'attributes'    => array(
			'data-conditional'     => wp_json_encode(array(
				'operator' => 'OR',
				'djacc_layout'=>'toolbar',
				'djacc_mobile_layout'=> 'toolbar',
			)),
		),
	) );

	$cmb->add_field( array(
		'name'    => esc_html__('[Popup] Offset top/bottom', 'dj-accessibility'),
		'desc'    => esc_html__('Space above and below the panel.', 'dj-accessibility'),
		'id'      => 'djacc_voff_popup',
		'default' => '20',
		'type'    => 'text_small',
		'attributes'    => array(
			'type' => 'number',
			'data-conditional'     => wp_json_encode(array(
				'operator' => 'OR',
				'djacc_layout'=>'popup',
				'djacc_mobile_layout'=> 'popup',
			)),
		),
	) );

	$cmb->add_field( array(
		'name'    => esc_html__('[Popup] Offset left/right', 'dj-accessibility'),
		'desc'    => esc_html__('Space to the left or right of the panel.', 'dj-accessibility'),
		'id'      => 'djacc_hoff_popup',
		'default' => '20',
		'type'    => 'text_small',
		'attributes'    => array(
			'type' => 'number',
			'data-conditional'     => wp_json_encode(array(
				'operator' => 'OR',
				'djacc_layout'=>'popup',
				'djacc_mobile_layout'=> 'popup',
			)),
		),
	) );

	if( $plugin_type ) {
		$cmb->add_field( array(
			'name'    => esc_html__('[Toolbar] Offset top/bottom', 'dj-accessibility'),
			'desc'    => esc_html__('Space above and below the panel.', 'dj-accessibility'),
			'id'      => 'djacc_voff_toolbar',
			'default' => '0',
			'type'    => 'text_small',
			'attributes'    => array(
				'type' => 'number',
				'data-conditional'     => wp_json_encode(array(
					'operator' => 'OR',
					'djacc_layout'=>'toolbar',
					'djacc_mobile_layout'=> 'toolbar',
				)),
			),
		) );

		$cmb->add_field( array(
			'name'    => esc_html__('[Toolbar] Offset left/right', 'dj-accessibility'),
			'desc'    => esc_html__('Space to the left or right of the panel.', 'dj-accessibility'),
			'id'      => 'djacc_hoff_toolbar',
			'default' => '0',
			'type'    => 'text_small',
			'attributes'    => array(
				'type' => 'number',
				'data-conditional'     => wp_json_encode(array(
					'operator' => 'OR',
					'djacc_layout'=>'toolbar',
					'djacc_mobile_layout'=> 'toolbar',
				)),
			),
		) );
	}

	$cmb->add_field( array(
		'name'    => esc_html__('[Popup] Button', 'dj-accessibility'),
		'id'      => 'djacc_image',
		'type'    => 'file',
		'text'    => array(
			'add_upload_file_text' => esc_html__('Add image', 'dj-accessibility')
		),
		// query_args are passed to wp.media's library query.
		'query_args' => array(
			// Or only allow gif, jpg, or png images
			// 'type' => array(
			// 	'image/gif',
			// 	'image/jpeg',
			// 	'image/png',
			// ),
		),
		'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
		'attributes'    => array(
			'data-conditional'     => wp_json_encode(array(
				'operator' => 'OR',
				'djacc_layout'=>'popup',
				'djacc_mobile_layout'=> 'popup',
			)),
		),
	) );

	$cmb->add_field( array(
		'name'    => esc_html__('Button width', 'dj-accessibility'),
		'id'      => 'djacc_width',
		'default' => '48',
		'type'    => 'text_small',
		'attributes'    => array(
			'type' => 'number',
			'data-conditional'     => wp_json_encode(array(
				'operator' => 'OR',
				'djacc_layout'=>'popup',
				'djacc_mobile_layout'=> 'popup',
			)),
		),
	) );

	$cmb->add_field( array(
		'name'    => esc_html__('Button height', 'dj-accessibility'),
		'id'      => 'djacc_height',
		'default' => '48',
		'type'    => 'text_small',
		'attributes'    => array(
			'type' => 'number',
			'data-conditional'     => wp_json_encode(array(
				'operator' => 'OR',
				'djacc_layout'=>'popup',
				'djacc_mobile_layout'=> 'popup',
			)),
		),
	) );

	$cmb->add_field( array(
		'name'             => esc_html__('Load Webfont', 'dj-accessibility'),
		'id'               => 'djacc_load_font',
		'type'             => 'select',
		'default'          => '1',
		'options'          => array(
			'1'  => esc_html__( 'Yes', 'dj-accessibility' ),
			'0' => esc_html__( 'No', 'dj-accessibility' ),
		),
	) );

	$cmb->add_field( array(
		'name'    => esc_html__('Webfont URL', 'dj-accessibility'),
		'desc'    => esc_html__('Example: https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap', 'dj-accessibility'),
		'id'      => 'djacc_font_url',
		'default' => 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap',
		'type'    => 'text',
		'attributes'    => array(
			'data-conditional'     => wp_json_encode(array(
				'djacc_load_font'=>'1',
			)),
		),
	) );

	$cmb->add_field( array(
		'name'    => esc_html__('Font family', 'dj-accessibility'),
		'desc'    => esc_html__('Example: Roboto, sans-serif', 'dj-accessibility'),
		'id'      => 'djacc_font_family',
		'default' => 'Roboto, sans-serif',
		'type'    => 'text',
		'attributes'    => array(
			'data-conditional'     => wp_json_encode(array(
				'djacc_load_font'=>'1',
			)),
		),
	) );

	if( $plugin_type ) {
		$cmb->add_field( array(
			'name'             => esc_html__('Screen reader advanced settings', 'dj-accessibility'),
			'id'               => 'djacc_speech_settings',
			'type'             => 'select',
			'default'          => '1',
			'options'          => array(
				'1'  => esc_html__( 'Show', 'dj-accessibility' ),
				'0' => esc_html__( 'Hide', 'dj-accessibility' ),
			),
		) );

		$cmb->add_field( array(
			'name'    => esc_html__('Speech pitch', 'dj-accessibility'),
			'desc'    => esc_html__('A float representing the pitch value. It can range between 0 (lowest) and 2 (highest), with 1 being the default pitch.', 'dj-accessibility'),
			'id'      => 'djacc_speech_pitch',
			'default' => '1',
			'type'    => 'text',
			'attributes'    => array(
				'data-conditional'     => wp_json_encode(array(
					'djacc_speech_settings'=>'1',
				)),
			),
		) );

		$cmb->add_field( array(
			'name'    => esc_html__('Speech rate', 'dj-accessibility'),
			'desc'    => esc_html__('A float representing the rate value. It can range between 0.1 (lowest) and 10 (highest), with 1 being the default.', 'dj-accessibility'),
			'id'      => 'djacc_speech_rate',
			'default' => '1',
			'type'    => 'text',
			'attributes'    => array(
				'data-conditional'     => wp_json_encode(array(
					'djacc_speech_settings'=>'1',
				)),
			),
		) );

		$cmb->add_field( array(
			'name'    => esc_html__('Speech volume', 'dj-accessibility'),
			'desc'    => esc_html__('A float that represents the volume value, between 0 (lowest) and 1 (highest).', 'dj-accessibility'),
			'id'      => 'djacc_speech_volume',
			'default' => '1',
			'type'    => 'text',
			'attributes'    => array(
				'data-conditional'     => wp_json_encode(array(
					'djacc_speech_settings'=>'1',
				)),
			),
		) );
	}

	$cl_group = $cmb->add_field( array(
			'id'          => 'djacc_custom_links',
			'type'        => 'group',
			'options'     => array(
					'group_title'   => __( 'Link', 'dj-accessibility' ) . ' {#}', // {#} gets replaced by row number
					'add_button'    => __( 'Add another Link', 'dj-accessibility' ),
					'remove_button' => __( 'Remove Link', 'dj-accessibility' ),
					'sortable'      => true, // beta
			),
	) );

	$cmb->add_group_field( $cl_group, array(
			'name'    => __( 'Name', 'dj-accessibility' ),
			'id'      => 'name',
			'type'    => 'text',
	) );
	$cmb->add_group_field( $cl_group, array(
		'name'    => __( 'URL address', 'dj-accessibility' ),
		'id'      => 'url',
		'type'    => 'text',
	) );
	$cmb->add_group_field( $cl_group, array(
		'name'             => esc_html__('Target attribute', 'dj-accessibility'),
		'id'               => 'target',
		'type'             => 'select',
		'default'          => '_self',
		'options'          => array(
			'_self'  => esc_html__( '_self', 'dj-accessibility' ),
			'_blank' => esc_html__( '_blank', 'dj-accessibility' ),
		),
	) );
	$cmb->add_group_field( $cl_group, array(
		'name'    => esc_html__('Image', 'dj-accessibility'),
		'id'      => 'image',
		'type'    => 'file',
		'text'    => array(
			'add_upload_file_text' => esc_html__('Add image', 'dj-accessibility')
		),
		'preview_size' => 'thumbnail',
	) );

}

function djacc_sanitize_checkbox($value, $field_args, $field) {
	// Return 0 instead of false if null value given.
	return is_null($value) ? 0 : $value;
}

?>