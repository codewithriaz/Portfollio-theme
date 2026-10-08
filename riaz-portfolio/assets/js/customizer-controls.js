/**
 * Customizer controls: repeater rows and the homepage section sorter.
 *
 * Values are stored as JSON strings in a single theme_mod each.
 */
( function ( api, $ ) {
	'use strict';

	var data = window.riazCustomizer || { icons: {}, sections: {}, i18n: {} };
	var i18n = data.i18n || {};

	function parseList( value ) {
		try {
			var parsed = JSON.parse( value );
			return Array.isArray( parsed ) ? parsed : [];
		} catch ( error ) {
			return [];
		}
	}

	function iconButton( action, icon, label ) {
		var button = document.createElement( 'button' );
		button.type = 'button';
		button.className = 'riaz-icon-button';
		button.setAttribute( 'data-action', action );
		button.setAttribute( 'aria-label', label );
		button.title = label;
		button.innerHTML = '<span class="dashicons dashicons-' + icon + '" aria-hidden="true"></span>';
		return button;
	}

	function sectionIdFor( slug ) {
		return 'contact' === slug ? 'riaz_contact_section' : 'riaz_' + slug;
	}

	/* ---------- Repeater ---------- */
	api.controlConstructor[ 'riaz-repeater' ] = api.Control.extend( {
		ready: function () {
			var control = this;
			var fields = control.params.fields || {};
			var keys = Object.keys( fields );
			var titleKey = keys.filter( function ( key ) {
				return 'icon' !== fields[ key ].type;
			} )[ 0 ];
			var max = parseInt( control.params.max, 10 ) || 0;
			var root = control.container[ 0 ].querySelector( '.riaz-repeater' );
			var list = root.querySelector( '.riaz-repeater-list' );
			var addButton = root.querySelector( '.riaz-repeater-add' );
			var rows = parseList( control.setting.get() );
			var timer = null;
			var openIndex = -1;

			function serialize() {
				return JSON.stringify( rows );
			}

			function save( immediate ) {
				window.clearTimeout( timer );
				if ( immediate ) {
					control.setting.set( serialize() );
				} else {
					timer = window.setTimeout( function () {
						control.setting.set( serialize() );
					}, 350 );
				}
			}

			function rowTitle( row, index ) {
				return ( titleKey && row[ titleKey ] ) || ( i18n.item || 'Item' ) + ' ' + ( index + 1 );
			}

			function buildInput( key, def, row ) {
				var input;
				if ( 'icon' === def.type ) {
					input = document.createElement( 'select' );
					Object.keys( data.icons ).forEach( function ( name ) {
						input.add( new Option( data.icons[ name ], name ) );
					} );
				} else if ( 'textarea' === def.type ) {
					input = document.createElement( 'textarea' );
					input.rows = 3;
				} else {
					input = document.createElement( 'input' );
					input.type = 'url' === def.type ? 'url' : 'text';
				}
				input.value = undefined === row[ key ] ? '' : row[ key ];
				return input;
			}

			function render() {
				list.innerHTML = '';
				rows.forEach( function ( row, index ) {
					var item = document.createElement( 'li' );
					var head = document.createElement( 'div' );
					var toggle = document.createElement( 'button' );
					var tools = document.createElement( 'span' );
					var body = document.createElement( 'div' );
					var isOpen = index === openIndex;

					item.className = 'riaz-repeater-row' + ( isOpen ? ' is-open' : '' );
					item.setAttribute( 'data-index', index );
					head.className = 'riaz-repeater-head';

					toggle.type = 'button';
					toggle.className = 'riaz-repeater-toggle';
					toggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
					toggle.textContent = rowTitle( row, index );

					tools.className = 'riaz-repeater-tools';
					tools.appendChild( iconButton( 'up', 'arrow-up-alt2', i18n.moveUp || 'Move up' ) );
					tools.appendChild( iconButton( 'down', 'arrow-down-alt2', i18n.moveDown || 'Move down' ) );
					tools.appendChild( iconButton( 'remove', 'trash', i18n.remove || 'Remove' ) );

					head.appendChild( toggle );
					head.appendChild( tools );

					body.className = 'riaz-repeater-body';
					body.hidden = ! isOpen;

					keys.forEach( function ( key ) {
						var def = fields[ key ];
						var label = document.createElement( 'label' );
						var caption = document.createElement( 'span' );
						var input = buildInput( key, def, row );

						label.className = 'riaz-repeater-field';
						caption.textContent = def.label;

						input.addEventListener( 'input', function () {
							row[ key ] = input.value;
							if ( key === titleKey ) {
								toggle.textContent = rowTitle( row, index );
							}
							save( false );
						} );
						input.addEventListener( 'change', function () {
							row[ key ] = input.value;
							save( true );
						} );

						label.appendChild( caption );
						label.appendChild( input );
						body.appendChild( label );
					} );

					toggle.addEventListener( 'click', function () {
						openIndex = body.hidden ? index : -1;
						body.hidden = ! body.hidden;
						toggle.setAttribute( 'aria-expanded', body.hidden ? 'false' : 'true' );
						item.classList.toggle( 'is-open', ! body.hidden );
					} );

					item.appendChild( head );
					item.appendChild( body );
					list.appendChild( item );
				} );

				var full = max > 0 && rows.length >= max;
				addButton.disabled = full;
				addButton.title = full ? i18n.max || '' : '';
			}

			addButton.addEventListener( 'click', function () {
				var row = {};
				keys.forEach( function ( key ) {
					row[ key ] = 'icon' === fields[ key ].type ? 'sparkles' : '';
				} );
				rows.push( row );
				openIndex = rows.length - 1;
				render();
				var first = list.lastElementChild && list.lastElementChild.querySelector( 'input, textarea, select' );
				if ( first ) {
					first.focus();
				}
				save( true );
			} );

			list.addEventListener( 'click', function ( event ) {
				var button = event.target.closest( '[data-action]' );
				if ( ! button ) {
					return;
				}
				var index = parseInt( button.closest( '.riaz-repeater-row' ).getAttribute( 'data-index' ), 10 );
				var action = button.getAttribute( 'data-action' );
				var target = 'up' === action ? index - 1 : index + 1;

				if ( 'remove' === action ) {
					rows.splice( index, 1 );
					openIndex = -1;
				} else if ( target >= 0 && target < rows.length ) {
					var moved = rows.splice( index, 1 )[ 0 ];
					rows.splice( target, 0, moved );
					openIndex = openIndex === index ? target : openIndex;
				}
				render();
				save( true );
			} );

			$( list ).sortable( {
				handle: '.riaz-repeater-head',
				cancel: 'button',
				axis: 'y',
				update: function () {
					var order = Array.prototype.map.call( list.children, function ( item ) {
						return parseInt( item.getAttribute( 'data-index' ), 10 );
					} );
					rows = order.map( function ( index ) {
						return rows[ index ];
					} );
					openIndex = -1;
					render();
					save( true );
				},
			} );

			control.setting.bind( function ( value ) {
				if ( value !== serialize() ) {
					rows = parseList( value );
					render();
				}
			} );

			render();
		},
	} );

	/* ---------- Section sorter ---------- */
	api.controlConstructor[ 'riaz-sortable' ] = api.Control.extend( {
		ready: function () {
			var control = this;
			var list = control.container[ 0 ].querySelector( '.riaz-sortable' );
			var labels = data.sections || {};
			var rows = parseList( control.setting.get() ).filter( function ( row ) {
				return row && labels[ row.id ];
			} );

			Object.keys( labels ).forEach( function ( id ) {
				var exists = rows.some( function ( row ) {
					return row.id === id;
				} );
				if ( ! exists ) {
					rows.push( { id: id, on: false } );
				}
			} );

			function save() {
				control.setting.set( JSON.stringify( rows ) );
			}

			function render() {
				list.innerHTML = '';
				rows.forEach( function ( row, index ) {
					var item = document.createElement( 'li' );
					var handle = document.createElement( 'span' );
					var label = document.createElement( 'label' );
					var checkbox = document.createElement( 'input' );
					var tools = document.createElement( 'span' );
					var edit = iconButton( 'edit', 'edit', ( i18n.edit || 'Edit' ) + ': ' + labels[ row.id ] );

					item.className = 'riaz-sortable-item' + ( row.on ? '' : ' is-off' );
					item.setAttribute( 'data-id', row.id );
					handle.className = 'riaz-sortable-handle dashicons dashicons-menu';
					handle.setAttribute( 'aria-hidden', 'true' );

					checkbox.type = 'checkbox';
					checkbox.checked = !! row.on;
					checkbox.addEventListener( 'change', function () {
						row.on = checkbox.checked;
						item.classList.toggle( 'is-off', ! row.on );
						save();
					} );
					label.appendChild( checkbox );
					label.appendChild( document.createTextNode( ' ' + labels[ row.id ] ) );

					tools.className = 'riaz-repeater-tools';
					tools.appendChild( iconButton( 'up', 'arrow-up-alt2', i18n.moveUp || 'Move up' ) );
					tools.appendChild( iconButton( 'down', 'arrow-down-alt2', i18n.moveDown || 'Move down' ) );
					tools.appendChild( edit );

					item.appendChild( handle );
					item.appendChild( label );
					item.appendChild( tools );
					item.setAttribute( 'data-index', index );
					list.appendChild( item );
				} );
			}

			list.addEventListener( 'click', function ( event ) {
				var button = event.target.closest( '[data-action]' );
				if ( ! button ) {
					return;
				}
				var item = button.closest( '.riaz-sortable-item' );
				var index = parseInt( item.getAttribute( 'data-index' ), 10 );
				var action = button.getAttribute( 'data-action' );

				if ( 'edit' === action ) {
					var section = api.section( sectionIdFor( item.getAttribute( 'data-id' ) ) );
					if ( section ) {
						section.focus();
					}
					return;
				}
				var target = 'up' === action ? index - 1 : index + 1;
				if ( target < 0 || target >= rows.length ) {
					return;
				}
				var moved = rows.splice( index, 1 )[ 0 ];
				rows.splice( target, 0, moved );
				render();
				save();
				var again = list.querySelector( '[data-index="' + target + '"] [data-action="' + action + '"]' );
				if ( again ) {
					again.focus();
				}
			} );

			$( list ).sortable( {
				handle: '.riaz-sortable-handle',
				axis: 'y',
				update: function () {
					var order = Array.prototype.map.call( list.children, function ( item ) {
						return item.getAttribute( 'data-id' );
					} );
					rows.sort( function ( a, b ) {
						return order.indexOf( a.id ) - order.indexOf( b.id );
					} );
					render();
					save();
				},
			} );

			render();
		},
	} );
} )( wp.customize, jQuery );
