( function( $, wp, blocks ) {
	const { serverSideRender } = wp;
	const { registerBlockType } = wp.blocks;
	const {
		PanelBody,
		PanelRow,
		TextControl,
	} = wp.components;
	const { InspectorControls } = wp.blockEditor;
	const { createElement, Fragment } = wp.element;

	function parseArgs( defaults, config ) {
		if ( 'object' !== typeof config ) {
			config = {};
		}

		if ( 'object' !== typeof defaults ) {
			defaults = {};
		}

		return $.extend({}, defaults, config);
	}

	function renderFields( props, config ) {
		const { attributes, setAttributes } = props;
		const fields = [];

		Object.keys( config.attributes ).forEach( name => {
			fields.push( createElement( PanelRow, {},
				createElement( TextControl,
					{
						label: config.attributes[ name ].label,
						onChange: (value) => {
							setAttributes( { [name]: value } );
						},
						value: attributes[ name ]
					}
				)
			) )
		} );

		return createElement( Fragment, null, fields );
	}

	function blockRegister( name, config ) {
		const defaults = {
			edit: ( props ) => {
				const { attributes } = props;

				return createElement(
					Fragment,
					null,
					createElement(
						InspectorControls,
						null,
						createElement( PanelBody, {},
							renderFields( props, config )
						)
					),
					createElement( serverSideRender, { block: name, attributes } ),
				);
			},
			save: () => null,
		};

		registerBlockType( name, parseArgs( defaults, config ) );
	}

	Object.keys( blocks ).forEach( name => {
		blockRegister( name, blocks[ name ] );
	} );
}(
	jQuery,
	wp,
	window.ThemePlate_Blocks
) );
