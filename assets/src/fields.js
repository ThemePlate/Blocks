import { Fragment } from '@wordpress/element';
import { TextControl } from '@wordpress/components';

export default function Fields( props ) {
	const { list, attributes, setAttributes } = props;

	return (
		<Fragment>
			{
				list.map( field => <TextControl
						key={ field.key }
						label={ field.title }
						value={ attributes[ field.key ] }
						onChange={ value => setAttributes( { [ field.key ]: value } ) }
					/>,
				)
			}
		</Fragment>
	);
}
