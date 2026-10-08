/**
 * Theme Section block (editor). Server-rendered, so the preview always
 * matches the front end. No build step required.
 */
( function ( wp ) {
	'use strict';

	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment } = wp.element;
	const { InspectorControls, useBlockProps } = wp.blockEditor;
	const { PanelBody, SelectControl, TextControl, Disabled } = wp.components;
	const ServerSideRender = wp.serverSideRender;
	const { __ } = wp.i18n;

	const sections = window.riazSections || {};
	const options = Object.keys( sections ).map( ( value ) => ( { value, label: sections[ value ] } ) );

	registerBlockType( 'riaz/section', {
		edit( { attributes, setAttributes } ) {
			const blockProps = useBlockProps( { className: 'riaz-section-block' } );

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Section', 'riaz-portfolio-core' ), initialOpen: true },
						el( SelectControl, {
							label: __( 'Section', 'riaz-portfolio-core' ),
							value: attributes.section,
							options,
							onChange: ( section ) => setAttributes( { section } ),
							__nextHasNoMarginBottom: true,
						} ),
						el( TextControl, {
							label: __( 'HTML anchor (optional)', 'riaz-portfolio-core' ),
							help: __( 'Lets menu links jump here, e.g. #services.', 'riaz-portfolio-core' ),
							value: attributes.anchorId,
							onChange: ( anchorId ) => setAttributes( { anchorId: anchorId.replace( /[^A-Za-z0-9_-]/g, '' ) } ),
							__nextHasNoMarginBottom: true,
						} ),
						el(
							'p',
							{ className: 'components-base-control__help' },
							__( 'Edit the content in Appearance → Customize → Homepage Sections, or in the matching post type (Services, Portfolio, FAQs…).', 'riaz-portfolio-core' )
						)
					)
				),
				el(
					'div',
					blockProps,
					el( Disabled, null, el( ServerSideRender, { block: 'riaz/section', attributes } ) )
				)
			);
		},
		save: () => null,
	} );
} )( window.wp );
