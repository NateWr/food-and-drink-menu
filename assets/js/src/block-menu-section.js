const { __ } = wp.i18n;
const {	registerBlockType } = wp.blocks;
const { SelectControl, PanelBody, ServerSideRender, Disabled } = wp.components;
const {	InspectorControls } = wp.editor;
const {	menuSectionOptions } = fdm_blocks;

registerBlockType( 'food-and-drink-menu/menu-section', {
	title: __( 'Menu Section', 'food-and-drink-menu' ),
	category: 'widgets',
	icon: <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="M7 0c-3.314 0-6 3.134-6 7 0 3.31 1.969 6.083 4.616 6.812l-0.993 16.191c-0.067 1.098 0.778 1.996 1.878 1.996h1c1.1 0 1.945-0.898 1.878-1.996l-0.993-16.191c2.646-0.729 4.616-3.502 4.616-6.812 0-3.866-2.686-7-6-7zM27.167 0l-1.667 10h-1.25l-0.833-10h-0.833l-0.833 10h-1.25l-1.667-10h-0.833v13c0 0.552 0.448 1 1 1h2.604l-0.982 16.004c-0.067 1.098 0.778 1.996 1.878 1.996h1c1.1 0 1.945-0.898 1.878-1.996l-0.982-16.004h2.604c0.552 0 1-0.448 1-1v-13h-0.833z" /></svg>,
	attributes: {
		id: {
			type: 'number',
			default: 0
		}
	},
	supports: {
		html: false,
	},
	edit( { attributes, setAttributes } ) {
		const { id } = attributes;

		function setId( id ) {
			setAttributes( { id: parseInt(id, 10) } );
		}

		return (
			<div>
				<InspectorControls>
					 <PanelBody>
						<SelectControl
							label={ __( 'Select a Menu Section', 'food-and-drink-menu' ) }
							value={ id }
							onChange={ setId }
							options={ menuSectionOptions }
						/>
					</PanelBody>
				</InspectorControls>
				{id && id !== '0' ? (
					<Disabled>
						<ServerSideRender block="food-and-drink-menu/menu-section" attributes={ attributes } />
					</Disabled>
				) : (
					<SelectControl
						label={ __( 'Select a Menu Section' ) }
						value={ 0 }
						onChange={ setId }
						options={ menuSectionOptions }
						className="fdm-block-select"
					/>
				)}
			</div>
		);
	},
	save() {
		return null;
	},
} );
