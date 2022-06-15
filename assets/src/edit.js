/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { InnerBlocks, InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, Placeholder, Spinner } from '@wordpress/components';
import { useMemo, useState, Fragment } from '@wordpress/element';
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * Internal dependencies
 */
import Blocks from './vars';
import Fields from './fields';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit( props ) {
	const [ fields, setFields ] = useState( [] );
	const blockProps = useBlockProps();
	const { attributes, setAttributes } = props;

	const query = () => {
		fetch( Blocks.ajax_url, {
			method: 'POST',
			body: new URLSearchParams( {
				_wpnonce: Blocks._wpnonce,
				action: 'themeplate_blocks_fields',
				block: blockProps[ 'data-type' ],
			} ),
		} )
			.then( response => response.json() )
			.then( response => setFields( response.data ) );
	};

	useMemo( query, [] );

	return (
		<Fragment>
			<InspectorControls>
				<PanelBody className={ 'themeplate-blocks-fields' }>
					{ 0 === fields.length &&
						<Placeholder><Spinner /></Placeholder> }
					{ 0 !== fields.length &&
						<Fields
							list={ fields }
							attributes={ attributes }
							setAttributes={ setAttributes }
						/>
					}
				</PanelBody>
			</InspectorControls>
			<div className={ 'wp-block-themeplate' }>
				<ServerSideRender
					block={ blockProps[ 'data-type' ] }
					attributes={ attributes }
					className={ 'block-editor-server-side-render' }
				/>
				<InnerBlocks />
			</div>
		</Fragment>
	);
}
