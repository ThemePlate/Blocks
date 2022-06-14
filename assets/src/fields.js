import { Fragment } from '@wordpress/element';
import { PanelRow } from '@wordpress/components';

import Field from './field';

export default function Fields( props ) {
	const { list, attributes, setAttributes } = props;

	return (
		<Fragment>
			{ list.map( field => (
				<PanelRow key={ field.key }>
					{ Field( field, attributes, setAttributes ) }
				</PanelRow>
			) ) }
		</Fragment>
	);
}
