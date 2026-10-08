/**
 * Customizer live preview: instant brand colors and site title, and
 * re-initialising front-end behaviour after a section is re-rendered.
 */
( function ( api ) {
	'use strict';

	var vars = ( window.riazPreview && window.riazPreview.colorVars ) || {};

	Object.keys( vars ).forEach( function ( key ) {
		api( key, function ( value ) {
			value.bind( function ( color ) {
				document.documentElement.style.setProperty( vars[ key ], color );
			} );
		} );
	} );

	api( 'blogname', function ( value ) {
		value.bind( function ( title ) {
			document.querySelectorAll( '.brand-text-label' ).forEach( function ( node ) {
				node.textContent = title;
			} );
		} );
	} );

	api.bind( 'preview-ready', function () {
		if ( ! api.selectiveRefresh ) {
			return;
		}
		api.selectiveRefresh.bind( 'partial-content-rendered', function ( placement ) {
			if ( window.RiazTheme && placement.container && placement.container[ 0 ] ) {
				window.RiazTheme.init( placement.container[ 0 ] );
			}
		} );
	} );
} )( wp.customize );
