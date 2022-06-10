( function( $, wp, blocks ) {
	const { serverSideRender } = wp;
	const { registerBlockType } = wp.blocks;
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

	function blockRegister( name, config ) {
		const defaults = {
			edit: ( { attributes } ) => {

				return createElement(
					Fragment,
					null,
					createElement(
						InspectorControls,
						null,
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
