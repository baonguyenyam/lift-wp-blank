<?php
/**
 * Redux Framework textarea config.
 * For full documentation, please visit: http://devs.wow-wp.com/
 *
 * @package Redux Framework
 */

defined( 'ABSPATH' ) || exit;

Redux::set_section(
	$opt_name,
	array(
		'title'      => esc_html__( 'Textarea', 'your-textdomain-here' ),
		'id'         => 'basic-textarea',
		'desc'       => esc_html__( 'For full documentation on this field, visit: ', 'your-textdomain-here' ) . '<a href="https://devs.wow-wp.com/core-fields/textarea.html" target="_blank">https://devs.wow-wp.com/core-fields/textarea.html</a>',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'       => 'opt-textarea',
				'type'     => 'textarea',
				'title'    => esc_html__( 'Textarea Option - HTML Validated Custom', 'your-textdomain-here' ),
				'subtitle' => esc_html__( 'Subtitle', 'your-textdomain-here' ),
				'desc'     => esc_html__( 'This is the description field, again good for additional info.', 'your-textdomain-here' ),
				'default'  => 'Default Text',
			),
		),
	)
);
