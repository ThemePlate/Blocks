import { __ } from '@wordpress/i18n';
import {
	__experimentalLinkControl as LinkControl,
	MediaUpload,
} from '@wordpress/block-editor';
import {
	BaseControl,
	Button,
	Card,
	CardFooter,
	CardMedia,
	CheckboxControl,
	ColorIndicator,
	ColorPicker,
	ExternalLink,
	Flex,
	FlexItem,
	Modal,
	RadioControl,
	RangeControl,
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
					multiple={ config.multiple }
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
									checked={ attributes[ config.key ].includes(
										option.value
									) }
									onChange={ ( event ) => {
										setAttributes( {
											[ config.key ]: config.options
												.filter( ( { value } ) => {
													if (
														option.value !== value
													) {
														return attributes[
															config.key
														].includes( value );
													}

													return event;
												} )
												.map( ( { value } ) => value ),
										} );
									} }
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
						<Flex gap={ 4 } align="flex-start" justify="flex-start">
							<FlexItem>
								<Button
									variant="secondary"
									onClick={ () => setOpen( true ) }
								>
									Pick
								</Button>
							</FlexItem>

							<FlexItem>
								<ColorIndicator
									colorValue={ attributes[ config.key ] }
									className="themeplate-color-indicator"
								/>
							</FlexItem>
						</Flex>

						{ isOpen && (
							<Modal
								focusOnMount
								shouldCloseOnEsc
								shouldCloseOnClickOutside
								title={ __( 'Insert/edit color' ) }
								onRequestClose={ () => setOpen( false ) }
							>
								<ColorPicker
									color={ attributes[ config.key ] }
									onChange={ ( value ) =>
										setAttributes( {
											[ config.key ]: value,
										} )
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
						<Flex gap={ 6 } align="center" justify="flex-start">
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
								title={ __( 'Insert/edit link' ) }
								onRequestClose={ () => setOpen( false ) }
							>
								<TextControl
									type="text"
									label={ __( 'Link text' ) }
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
			const items = (
				config.multiple
					? attributes[ config.key ]
					: [ attributes[ config.key ] ]
			).filter( ( item ) => !! item );

			return (
				<BaseControl>
					<BaseControl.VisualLabel>
						{ config.title }
					</BaseControl.VisualLabel>

					<MediaUpload
						label={ config.title }
						multiple={ config.multiple }
						value={
							config.multiple
								? attributes[ config.key ].map(
										( { id } ) => id
								  )
								: attributes[ config.key ].id
						}
						onSelect={ ( value ) => {
							const saveValue = Array.isArray( value )
								? value.map(
										( { id, url, title, type, icon } ) => {
											return {
												id,
												url,
												title,
												type,
												icon,
											};
										}
								  )
								: {
										id: value.id,
										url: value.url,
										title: value.title,
										type: value.type,
										icon: value.icon,
								  };

							setAttributes( {
								[ config.key ]: saveValue,
							} );
						} }
						render={ ( { open } ) => (
							<Flex gap={ 4 } wrap={ true }>
								<FlexItem isBlock={ true }>
									<Button
										variant="secondary"
										onClick={ open }
									>
										{ __( 'Select' ) }
									</Button>
								</FlexItem>

								{ items.length >= 1 && (
									<FlexItem isBlock={ false }>
										<Button
											variant="secondary"
											onClick={ () => {
												setAttributes( {
													[ config.key ]:
														config.multiple
															? []
															: '',
												} );
											} }
										>
											{ config.multiple &&
											items.length > 1
												? __( 'Clear' )
												: __( 'Remove' ) }
										</Button>
									</FlexItem>
								) }

								<Flex gap={ 4 } direction={ 'column' }>
									{ items.map(
										( { url, title, type, icon } ) => (
											<FlexItem isBlock={ true }>
												<Card>
													<CardMedia>
														<img
															className="themeplate-image"
															src={
																'image' === type
																	? url
																	: icon
															}
														/>
													</CardMedia>
													<CardFooter>
														{ title }
													</CardFooter>
												</Card>
											</FlexItem>
										)
									) }
								</Flex>
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
