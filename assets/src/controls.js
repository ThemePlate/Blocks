import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, Placeholder, Spinner } from '@wordpress/components';
import { Fragment } from '@wordpress/element';

import Fields from './fields';

const LOCATIONS = [ 'default', 'styles' ];

export default function Controls( props ) {
	const { queried, fields, attributes, setAttributes } = props;

	return (
		<Fragment>
			{ LOCATIONS.map( ( location ) => (
				<InspectorControls group={ location }>
					{ ! queried && ( ! fields[ location ] || 0 === fields[ location ].length ) && (
						<Placeholder>
							<Spinner />
						</Placeholder>
					) }

					{ queried && 0 !== fields[ location ].length && (
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
					) }
				</InspectorControls>
			) ) }
		</Fragment>
	);
}
