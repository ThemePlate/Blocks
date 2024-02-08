import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, Placeholder, Spinner } from '@wordpress/components';
import { Fragment } from '@wordpress/element';

import Blocks from './vars';
import Fields from './fields';

export default function Controls( props ) {
	const { queried, fields, attributes, setAttributes } = props;

	if ( ! queried ) {
		return (
			<InspectorControls>
				<Placeholder>
					<Spinner />
				</Placeholder>
			</InspectorControls>
		);
	}

	return (
		<Fragment>
			{ Blocks.locations.map( ( location ) => (
				<Fragment>
					{ 0 !== fields[ location ].length && (
						<InspectorControls group={ location }>
							<PanelBody
								title={ __( 'Settings' ) }
								className="themeplate-blocks-fields"
							>
								<Fields
									list={ fields[ location ] }
									attributes={ attributes }
									setAttributes={ setAttributes }
								/>
							</PanelBody>
						</InspectorControls>
					) }
				</Fragment>
			) ) }
		</Fragment>
	);
}
