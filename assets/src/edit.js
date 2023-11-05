/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import {
	InnerBlocks,
	InspectorControls,
	store,
	useBlockProps,
} from '@wordpress/block-editor';
import { getBlockType } from '@wordpress/blocks';
import { PanelBody, Placeholder, Spinner } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import {
	useEffect,
	useMemo,
	useState,
	useRef,
	Fragment,
} from '@wordpress/element';
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
 * @param {Element} props
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit( props ) {
	const blockRef = useRef();
	const innerRef = useRef();
	const [ fields, setFields ] = useState( [] );
	const [ queried, setQueried ] = useState( false );
	const blockProps = useBlockProps( {
		className: 'wp-block-themeplate',
		ref: blockRef,
	} );
	const blockID = blockProps[ 'data-type' ];
	const { attributes, setAttributes } = props;
	const currentBlock = useSelect(
		( select ) => select( store ).getBlock( props.clientId ),
		[ props ]
	);
	const blockType = getBlockType( currentBlock.name );
	const supportsInnerBlocks = Blocks.collection[ blockID ].inner_blocks;
	const hasInnerBlocks = !! (
		currentBlock && currentBlock?.innerBlocks?.length
	);

	useMemo( () => {
		fetch( Blocks.ajax_url, {
			method: 'POST',
			body: new URLSearchParams( {
				_wpnonce: Blocks._wpnonce,
				action: 'themeplate_blocks_fields',
				block: blockID,
			} ),
		} )
			.then( ( response ) => response.json() )
			.then( ( response ) => {
				setFields( response.data );
				setQueried( true );
			} );
	}, [ blockID ] );

	if ( supportsInnerBlocks ) {
		useEffect( () => {
			/* global MutationObserver */
			const observer = new MutationObserver( () => {
				const innerBlocks = blockRef.current.querySelector(
					'ThemePlateInnerBlocks'
				);

				if ( null === innerBlocks || innerBlocks.childNodes.length ) {
					return;
				}

				innerBlocks.parentNode.replaceChild(
					innerRef.current,
					innerBlocks
				);
			} );

			observer.observe( blockRef.current, { childList: true } );

			return () => {
				observer.disconnect();
			};
		}, [] );
	}

	return (
		<Fragment>
			<InspectorControls>
				{ ! queried && 0 === fields.length && (
					<Placeholder>
						<Spinner />
					</Placeholder>
				) }

				{ queried && 0 !== fields.length && (
					<PanelBody className={ 'themeplate-blocks-fields' }>
						<Fields
							list={ fields }
							attributes={ attributes }
							setAttributes={ setAttributes }
						/>
					</PanelBody>
				) }
			</InspectorControls>

			<div { ...blockProps }>
				<ServerSideRender
					block={ blockID }
					attributes={ attributes }
					className={ 'block-editor-server-side-render' }
				/>

				{ supportsInnerBlocks && (
					<InnerBlocks
						ref={ innerRef }
						allowedBlocks={ blockType.allowed_blocks }
						template={ blockType.template_blocks }
						templateLock={ blockType.template_lock }
						renderAppender={
							hasInnerBlocks
								? null
								: InnerBlocks.ButtonBlockAppender
						}
					/>
				) }
			</div>
		</Fragment>
	);
}
