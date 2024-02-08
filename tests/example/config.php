<?php

/**
 * @package ThemePlate
 */

$fields = array(
	'text'     => array(
		'title'       => __( 'Text', 'themeplate' ),
		'description' => __( 'Enter a text.', 'themeplate' ),
		'type'        => 'text',
	),
	'textarea' => array(
		'title'       => __( 'Textarea', 'themeplate' ),
		'description' => __( 'Enter in a textarea.', 'themeplate' ),
		'type'        => 'textarea',
	),
	'link'     => array(
		'title'       => __( 'Link', 'themeplate' ),
		'description' => __( 'Select a link.', 'themeplate' ),
		'type'        => 'link',
	),
	'selects'  => array(
		'title'       => __( 'Selects', 'themeplate' ),
		'description' => __( 'Basic and Advanced select.', 'themeplate' ),
		'location'    => 'styles',
		'type'        => 'group',
		'fields'      => array(
			'single'   => array(
				'title'       => __( 'Single', 'themeplate' ),
				'description' => __( 'Select a value.', 'themeplate' ),
				'options'     => array( 'One', 'Two', 'Three' ),
				'type'        => 'select',
			),
			'multiple' => array(
				'title'       => __( 'Multiple', 'themeplate' ),
				'description' => __( 'Select values.', 'themeplate' ),
				'options'     => array( 'One', 'Two', 'Three', 'Four', 'Five', 'Six' ),
				'type'        => 'select',
				'multiple'    => true,
			),
		),
	),
	'choices'  => array(
		'title'       => __( 'Choices', 'themeplate' ),
		'description' => __( 'Single and Multiple choices.', 'themeplate' ),
		'location'    => 'styles',
		'type'        => 'group',
		'style'       => 'boxed',
		'fields'      => array(
			'radio'      => array(
				'title'       => __( 'Radio', 'themeplate' ),
				'description' => __( 'Select from radio.', 'themeplate' ),
				'options'     => array( 'First', 'Second', 'Third' ),
				'type'        => 'radio',
			),
			'checkboxes' => array(
				'title'       => __( 'Checkboxes', 'themeplate' ),
				'description' => __( 'Single and Multiple checkbox.', 'themeplate' ),
				'type'        => 'group',
				'fields'      => array(
					'single'   => array(
						'title' => __( 'Single?', 'themeplate' ),
						'type'  => 'checkbox',
					),
					'multiple' => array(
						'title'       => __( 'Multiple', 'themeplate' ),
						'description' => __( 'Check a box.', 'themeplate' ),
						'options'     => array( 'Uno', 'Dos', 'Tres' ),
						'type'        => 'checklist',
					),
				),
			),
		),
	),
	'color'    => array(
		'title'       => __( 'Color', 'themeplate' ),
		'description' => __( 'Select a color.', 'themeplate' ),
		'location'    => 'unknown',
		'type'        => 'color',
	),
	'files'    => array(
		'title'       => __( 'Files', 'themeplate' ),
		'description' => __( 'Single and Multiple file.', 'themeplate' ),
		'type'        => 'group',
		'fields'      => array(
			'single'   => array(
				'title'       => __( 'Single', 'themeplate' ),
				'description' => __( 'Select a file.', 'themeplate' ),
				'type'        => 'file',
			),
			'multiple' => array(
				'title'       => __( 'Multiple', 'themeplate' ),
				'description' => __( 'Select files.', 'themeplate' ),
				'type'        => 'file',
				'multiple'    => true,
			),
		),
	),
	'range'    => array(
		'title'       => __( 'Range', 'themeplate' ),
		'description' => __( 'Set a range.', 'themeplate' ),
		'type'        => 'range',
	),
	'html'     => array(
		'type'    => 'html',
		'default' => '
				<div style="background-color: #d32f2f; padding: 1rem; border-radius: 0.25rem;">
					<p style="color: #fff; text-align: center;">Display custom content using an <code>html</code> field.</p>
				</div>
			',
	),
);

return array(
	'inner_blocks'  => false,
	'custom_fields' => $fields,
);
