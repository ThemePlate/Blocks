/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import Edit from './edit';
import Save from './save';
import Blocks from './vars';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */

Object.keys( Blocks.collection ).forEach( ( name ) => {
	registerBlockType( name, {
		apiVersion: 2,
		edit: Edit,
		save: Save,

		...Blocks.collection[ name ],
	} );
} );
