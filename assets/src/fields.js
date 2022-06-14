import { Fragment } from '@wordpress/element';
import { PanelRow } from '@wordpress/components';

import Field from './field';

export default function Fields( props ) {
	const { list, attributes, setAttributes } = props;

	return (
		<Fragment>
			{ list.map( field => (
				<PanelRow
					key={ field.key }
					className={ [ 'themeplate-blocks-field', `field-${ field.class }` ] }
				>
					{ Field( field, attributes, setAttributes ) }
				</PanelRow>
			) ) }
		</Fragment>
	);
}
