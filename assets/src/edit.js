/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { BlockControls, InnerBlocks, InspectorControls, store, useBlockProps } from '@wordpress/block-editor';
import { getBlockContent, getBlockType } from '@wordpress/blocks';
import { PanelBody, Placeholder, Spinner, ToolbarButton, ToolbarGroup } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
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
	const [ preview, setPreview ] = useState( true );
	const [ queried, setQueried ] = useState( false );
	const blockProps = useBlockProps();
	const { attributes, setAttributes } = props;
	const currentBlock = useSelect(
		select => select( store ).getBlock( props.clientId ),
		[ props ],
	);
	const blockType = getBlockType( currentBlock.name );
	const innerBlockContent = getBlockContent( currentBlock );
	const supportsInnerBlocks = Blocks.collection[ blockProps[ 'data-type' ] ].inner_blocks;

	const handleDoubleClick = event => {
		const targetDataset = event.target.dataset;

		if ( targetDataset?.block && targetDataset?.type ) {
			return;
		}

		setPreview( !preview );
	};

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
			.then( response => {
				setFields( response.data );
				setQueried( true );
			} );
	};

	useMemo( query, [] );

	return (
		<Fragment>
			<InspectorControls>
				{ ( ! queried && 0 === fields.length ) &&
					<Placeholder><Spinner /></Placeholder>
				}

				{ ( queried && 0 !== fields.length ) &&
					<PanelBody className={ 'themeplate-blocks-fields' }>
						<Fields
							list={ fields }
							attributes={ attributes }
							setAttributes={ setAttributes }
						/>
					</PanelBody>
				}
			</InspectorControls>

			{ supportsInnerBlocks &&
				<BlockControls>
					<ToolbarGroup>
						<ToolbarButton
							icon={ preview ? 'visibility' : 'hidden' }
							label={ preview ? 'Switch to insert inner blocks' : 'Switch to preview rendered block' }
							onClick={ () => setPreview( !preview ) }
						/>
					</ToolbarGroup>
				</BlockControls>
			}

			<div className={ 'wp-block-themeplate' } onDoubleClick={ handleDoubleClick }>
				{ ( ! supportsInnerBlocks || true === preview ) &&
					<ServerSideRender
						block={ blockProps[ 'data-type' ] }
						attributes={ { ...attributes, innerBlockContent } }
						className={ 'block-editor-server-side-render' }
					/>
				}

				{ ( supportsInnerBlocks && false === preview ) &&
					<InnerBlocks
						allowedBlocks={ blockType[ 'allowed_blocks' ] }
						template={ blockType[ 'template_blocks' ] }
						templateLock={ blockType[ 'template_lock' ] }
						renderAppender={ InnerBlocks.ButtonBlockAppender }
					/>
				}
			</div>
		</Fragment>
	);
}
