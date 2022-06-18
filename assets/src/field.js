import {
	BaseControl,
	CheckboxControl,
	ColorPicker,
	RadioControl,
	RangeControl,
	SelectControl,
	TextareaControl,
	TextControl,
	Tip,
} from '@wordpress/components';
import { Fragment, RawHTML } from '@wordpress/element';

import Fields from './fields';

const Field = ( config, attributes, setAttributes ) => {
	switch ( config.type ) {
		default:
		case 'text':
		case 'time':
		case 'email':
		case 'url':
		case 'date':
		case 'number':
			return (
				<TextControl
					type={ config.type }
					label={ config.title }
					help={ config?.help || '' }
					value={ attributes[ config.key ] }
					onChange={ value => setAttributes( { [ config.key ]: value } ) }
				/>
			);

		case 'textarea':
			return (
				<TextareaControl
					label={ config.title }
					help={ config?.help || '' }
					value={ attributes[ config.key ] }
					onChange={ value => setAttributes( { [ config.key ]: value } ) }
				/>
			);

		case 'select':
		case 'select2':
			return (
				<SelectControl
					label={ config.title }
					help={ config?.help || '' }
					value={ attributes[ config.key ] }
					options={ config.options }
					onChange={ value => setAttributes( { [ config.key ]: value } ) }
				/>
			);

		case 'radiolist':
		case 'radio':
			return (
				<RadioControl
					label={ config.title }
					help={ config?.help || '' }
					selected={ attributes[ config.key ] }
					options={ config.options }
					onChange={ value => setAttributes( { [ config.key ]: value } ) }
				/>
			);

		case 'checklist':
		case 'checkbox':
			return (
				<BaseControl help={ config?.help || '' }>
					<BaseControl.VisualLabel>
						{ config.title }
					</BaseControl.VisualLabel>

					{ 0 === config.options.length &&
						<CheckboxControl
							label={ config.title }
							checked={ 'true' === attributes[ config.key ] }
							onChange={ value => setAttributes( { [ config.key ]: value.toString() } ) }
						/>
					}
					{ 0 !== config.options.length &&
						<Fragment>
							{ config.options.map( option => (
								<CheckboxControl
									key={ option.value }
									label={ option.label }
									checked={ attributes[ config.key ] === option.value }
									onChange={ () => setAttributes( { [ config.key ]: option.value } ) }
								/>
							) ) }
						</Fragment>
					}
				</BaseControl>
			);

		case 'color':
			return (
				<BaseControl help={ config?.help || '' }>
					<BaseControl.VisualLabel>
						{ config.title }
					</BaseControl.VisualLabel>

					<ColorPicker
						color={ attributes[ config.key ] }
						onChange={ value => setAttributes( { [ config.key ]: value } ) }
						enableAlpha={ true }
					/>
				</BaseControl>
			);

		case 'range':
			return (
				<RangeControl
					label={ config.title }
					help={ config?.help || '' }
					value={ parseInt( attributes[ config.key ] ) }
					onChange={ value => setAttributes( { [ config.key ]: value.toString() } ) }
					afterIcon={ <strong>{ attributes[ config.key ] }</strong> }
					withInputField={ false }
				/>
			);

		case 'html':
			return (
				<RawHTML>{ attributes[ config.key ] }</RawHTML>
			);

		case 'group':
			return (
				<BaseControl help={ config?.help || '' }>
					<BaseControl.VisualLabel>
						{ config.title }
					</BaseControl.VisualLabel>

					<Fields
						list={ config.fields }
						attributes={ attributes[ config.key ] }
						setAttributes={ values => {
							setAttributes( {
								[ config.key ]: { ...attributes[ config.key ], ...values },
							} );
						} }
					/>
				</BaseControl>
			);

		case 'file':
		case 'editor':
		case 'type':
		case 'post':
		case 'page':
		case 'user':
		case 'term':
		case 'link':
			return (
				<BaseControl help={ config?.help || '' }>
					<BaseControl.VisualLabel>
						{ config.title }
					</BaseControl.VisualLabel>

					<Tip><strong>TODO!</strong> Field <code>{ config.type }</code></Tip>
				</BaseControl>
			);
	}
};

export default Field;
