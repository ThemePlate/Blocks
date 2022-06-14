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
					value={ attributes[ config.key ] }
					onChange={ value => setAttributes( { [ config.key ]: value } ) }
				/>
			);

		case 'textarea':
			return (
				<TextareaControl
					label={ config.title }
					value={ attributes[ config.key ] }
					onChange={ value => setAttributes( { [ config.key ]: value } ) }
				/>
			);

		case 'select':
		case 'select2':
			return (
				<SelectControl
					label={ config.title }
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
					selected={ attributes[ config.key ] }
					options={ config.options }
					onChange={ value => setAttributes( { [ config.key ]: value } ) }
				/>
			);

		case 'checklist':
		case 'checkbox':
			return (
				<BaseControl>
					<BaseControl.VisualLabel>
						{ config.title }
					</BaseControl.VisualLabel>

					<CheckboxControl
						label={ config.title }
						value={ attributes[ config.key ] }
						onChange={ value => setAttributes( { [ config.key ]: value } ) }
					/>
				</BaseControl>
			);

		case 'color':
			return (
				<BaseControl>
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
					value={ attributes[ config.key ] }
					onChange={ value => setAttributes( { [ config.key ]: value } ) }
					afterIcon={ <span>{ attributes[ config.key ] }</span> }
					withInputField={ false }
				/>
			);

		case 'html':
			return (
				<RawHTML>{ attributes[ config.key ] }</RawHTML>
			);

		case 'group':
		case 'file':
		case 'editor':
		case 'type':
		case 'post':
		case 'page':
		case 'user':
		case 'term':
		case 'link':
			return (
				<BaseControl>
					<BaseControl.VisualLabel>
						{ config.title }
					</BaseControl.VisualLabel>

					<Tip><strong>TODO!</strong> Field <code>{ config.type }</code></Tip>
				</BaseControl>
			);
	}
};

export default Field;
