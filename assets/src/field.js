import {
	__experimentalLinkControl as LinkControl,
	MediaUpload,
} from '@wordpress/block-editor';
import {
	BaseControl,
	Button,
	CheckboxControl,
	ColorIndicator,
	ColorPicker,
	ExternalLink,
	Flex,
	FlexItem,
	Modal,
	Placeholder,
	RadioControl,
	RangeControl,
	ResponsiveWrapper,
	SelectControl,
	TextareaControl,
	TextControl,
	Tip,
} from '@wordpress/components';
import { Fragment, RawHTML, useState } from '@wordpress/element';

import Fields from './fields';

const Field = ( config, attributes, setAttributes ) => {
	const [ isOpen, setOpen ] = useState( false );

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
					onChange={ ( value ) =>
						setAttributes( { [ config.key ]: value } )
					}
				/>
			);

		case 'textarea':
			return (
				<TextareaControl
					label={ config.title }
					help={ config?.help || '' }
					value={ attributes[ config.key ] }
					onChange={ ( value ) =>
						setAttributes( { [ config.key ]: value } )
					}
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
					onChange={ ( value ) =>
						setAttributes( { [ config.key ]: value } )
					}
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
					onChange={ ( value ) =>
						setAttributes( { [ config.key ]: value } )
					}
				/>
			);

		case 'checklist':
		case 'checkbox':
			return (
				<BaseControl help={ config?.help || '' }>
					<BaseControl.VisualLabel>
						{ config.title }
					</BaseControl.VisualLabel>

					{ 0 === config.options.length && (
						<CheckboxControl
							checked={ 'true' === attributes[ config.key ] }
							onChange={ ( value ) =>
								setAttributes( {
									[ config.key ]: value.toString(),
								} )
							}
						/>
					) }
					{ 0 !== config.options.length && (
						<Fragment>
							{ config.options.map( ( option ) => (
								<CheckboxControl
									key={ option.value }
									label={ option.label }
									checked={
										attributes[ config.key ] ===
										option.value
									}
									onChange={ () =>
										setAttributes( {
											[ config.key ]: option.value,
										} )
									}
								/>
							) ) }
						</Fragment>
					) }
				</BaseControl>
			);

		case 'color':
			return (
				<BaseControl help={ config?.help || '' }>
					<BaseControl.VisualLabel>
						{ config.title }
					</BaseControl.VisualLabel>

					<Fragment>
						<Flex
							gap={ 6 }
							align={ 'center' }
							justify={ 'flex-start' }
						>
							<FlexItem>
								<Button
									variant="secondary"
									onClick={ () => setOpen( true ) }
								>
									Pick
								</Button>
							</FlexItem>

							<FlexItem>
								<ColorIndicator colorValue={ attributes[ config.key ] } />
							</FlexItem>
						</Flex>

						{ isOpen && (
							<Modal
								focusOnMount
								shouldCloseOnEsc
								shouldCloseOnClickOutside
								title={ 'Insert/edit color' }
								onRequestClose={ () => setOpen( false ) }
							>
								<ColorPicker
									color={ attributes[ config.key ] }
									onChange={ ( value ) =>
										setAttributes( { [ config.key ]: value } )
									}
									enableAlpha={ true }
								/>
							</Modal>
						) }
					</Fragment>
				</BaseControl>
			);

		case 'range':
			return (
				<RangeControl
					label={ config.title }
					help={ config?.help || '' }
					value={ parseInt( attributes[ config.key ] ) }
					onChange={ ( value ) =>
						setAttributes( { [ config.key ]: value.toString() } )
					}
					afterIcon={ <strong>{ attributes[ config.key ] }</strong> }
					withInputField={ false }
				/>
			);

		case 'html':
			return <RawHTML>{ attributes[ config.key ] }</RawHTML>;

		case 'group':
			return (
				<BaseControl help={ config?.help || '' }>
					<BaseControl.VisualLabel>
						{ config.title }
					</BaseControl.VisualLabel>

					<Fields
						list={ config.fields }
						attributes={ attributes[ config.key ] }
						setAttributes={ ( values ) => {
							setAttributes( {
								[ config.key ]: {
									...attributes[ config.key ],
									...values,
								},
							} );
						} }
					/>
				</BaseControl>
			);

		case 'link':
			return (
				<BaseControl help={ config?.help || '' }>
					<BaseControl.VisualLabel>
						{ config.title }
					</BaseControl.VisualLabel>

					<Fragment>
						<Flex
							gap={ 6 }
							align={ 'center' }
							justify={ 'flex-start' }
						>
							<FlexItem>
								<Button
									variant="secondary"
									onClick={ () => setOpen( true ) }
								>
									Select
								</Button>
							</FlexItem>

							<FlexItem>
								<ExternalLink
									href={ attributes[ config.key ].url }
								>
									{ attributes[ config.key ].title }
								</ExternalLink>
							</FlexItem>
						</Flex>

						{ isOpen && (
							<Modal
								focusOnMount
								shouldCloseOnEsc
								shouldCloseOnClickOutside
								title={ 'Insert/edit link' }
								onRequestClose={ () => setOpen( false ) }
							>
								<TextControl
									type={ 'text' }
									label={ 'Link text' }
									value={ attributes[ config.key ].title }
									onChange={ ( value ) =>
										setAttributes( {
											[ config.key ]: {
												...attributes[ config.key ],
												title: value,
											},
										} )
									}
								/>

								<LinkControl
									value={ attributes[ config.key ] }
									onChange={ ( value ) =>
										setAttributes( {
											[ config.key ]: value,
										} )
									}
								/>
							</Modal>
						) }
					</Fragment>
				</BaseControl>
			);

		case 'file':
			return (
				<BaseControl>
					<BaseControl.VisualLabel>
						{ config.title }
					</BaseControl.VisualLabel>

					<MediaUpload
						label={ config.title }
						value={ attributes[ config.key ].id }
						onSelect={ ( value ) =>
							setAttributes( {
								[ config.key ]: {
									id: value.id,
									url: value.url,
									title: value.title,
								},
							} )
						}
						render={ ( { open } ) => (
							<Flex
								gap={ 4 }
								align={ 'center' }
								justify={ 'flex-start' }
							>
								<FlexItem>
									<Button
										variant="secondary"
										onClick={ open }
									>
										Select
									</Button>
								</FlexItem>

								<FlexItem isBlock={ true }>
									<Placeholder>
										{ attributes[ config.key ]?.url && (
											<ResponsiveWrapper>
												<img
													src={
														attributes[ config.key ]
															.url
													}
													alt={
														attributes[ config.key ]
															.title
													}
												/>
											</ResponsiveWrapper>
										) }
									</Placeholder>
								</FlexItem>
							</Flex>
						) }
					/>
				</BaseControl>
			);

		case 'editor':
		case 'type':
		case 'post':
		case 'page':
		case 'user':
		case 'term':
			return (
				<BaseControl help={ config?.help || '' }>
					<BaseControl.VisualLabel>
						{ config.title }
					</BaseControl.VisualLabel>

					<Tip>
						<strong>TODO!</strong> Field{ ' ' }
						<code>{ config.type }</code>
					</Tip>
				</BaseControl>
			);
	}
};

export default Field;
