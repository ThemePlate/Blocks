import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	PanelRow,
	Placeholder,
	Spinner,
} from '@wordpress/components';
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
				<Fragment key={ location }>
					{ 0 !== fields[ location ].length && (
						<InspectorControls group={ location }>
							<Fragment>
								{ ( 'advanced' !== location && (
									<PanelBody title={ __( 'Control Fields' ) }>
										<Fields
											list={ fields[ location ] }
											attributes={ attributes }
											setAttributes={ setAttributes }
										/>
									</PanelBody>
								) ) || (
									<Fragment>
										<Fields
											list={ fields[ location ] }
											attributes={ attributes }
											setAttributes={ setAttributes }
										/>

										<PanelRow
											className={ [
												'themeplate-blocks-field',
												`field-type-separator`,
											] }
										/>
									</Fragment>
								) }
							</Fragment>
						</InspectorControls>
					) }
				</Fragment>
			) ) }
		</Fragment>
	);
}
