/**
 * Riaz Portfolio — front-end behaviour.
 *
 * Dependency-free progressive enhancement: every feature degrades to plain,
 * readable HTML when JavaScript is unavailable. Motion only uses opacity and
 * transform and respects prefers-reduced-motion.
 */
( function () {
	'use strict';

	const doc = document.documentElement;
	const body = document.body;
	const reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' );
	const EASE = 'cubic-bezier(0.22, 1, 0.36, 1)';

	// Tells the head bootstrap that reveals are handled (see inc/enqueue.php).
	window.riazReady = true;

	/* ---------- Utilities ---------- */
	const $$ = ( root, selector ) => Array.prototype.slice.call( root.querySelectorAll( selector ) );

	const once = ( el, key ) => {
		if ( el.dataset[ key ] ) {
			return false;
		}
		el.dataset[ key ] = '1';
		return true;
	};

	const scrollCallbacks = [];
	let scrollTicking = false;
	const onScroll = ( fn ) => scrollCallbacks.push( fn );
	const runScroll = () => {
		scrollTicking = false;
		scrollCallbacks.forEach( ( fn ) => fn() );
	};
	const requestScroll = () => {
		if ( ! scrollTicking ) {
			scrollTicking = true;
			window.requestAnimationFrame( runScroll );
		}
	};
	window.addEventListener( 'scroll', requestScroll, { passive: true } );
	window.addEventListener( 'resize', requestScroll );

	let liveRegion = null;
	const announce = ( message ) => {
		if ( ! liveRegion ) {
			liveRegion = document.createElement( 'div' );
			liveRegion.className = 'screen-reader-text';
			liveRegion.setAttribute( 'aria-live', 'polite' );
			body.appendChild( liveRegion );
		}
		liveRegion.textContent = '';
		window.setTimeout( () => {
			liveRegion.textContent = message;
		}, 60 );
	};

	/* ---------- Header: scrolled state + mobile menu ---------- */
	function initHeader() {
		const header = document.querySelector( '[data-header]' );
		if ( ! header ) {
			return;
		}

		const updateScrolled = () => header.classList.toggle( 'is-scrolled', window.scrollY > 40 );
		updateScrolled();
		onScroll( updateScrolled );

		const toggle = header.querySelector( '[data-menu-toggle]' );
		const panel = header.querySelector( '[data-mobile-menu]' );
		if ( ! toggle || ! panel ) {
			return;
		}
		const label = toggle.querySelector( '.screen-reader-text' );
		const isOpen = () => 'true' === toggle.getAttribute( 'aria-expanded' );

		const setOpen = ( open, returnFocus ) => {
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			if ( label ) {
				label.textContent = open ? toggle.dataset.labelClose : toggle.dataset.labelOpen;
			}
			header.classList.toggle( 'menu-open', open );
			if ( open ) {
				panel.hidden = false;
				window.requestAnimationFrame( () => panel.classList.add( 'is-open' ) );
			} else {
				panel.classList.remove( 'is-open' );
				window.setTimeout( () => {
					if ( ! isOpen() ) {
						panel.hidden = true;
					}
				}, 260 );
				if ( returnFocus ) {
					toggle.focus();
				}
			}
		};

		toggle.addEventListener( 'click', () => setOpen( ! isOpen() ) );
		panel.addEventListener( 'click', ( event ) => {
			if ( event.target.closest( 'a' ) ) {
				setOpen( false );
			}
		} );
		document.addEventListener( 'keydown', ( event ) => {
			if ( 'Escape' === event.key && isOpen() ) {
				setOpen( false, true );
			}
		} );
		document.addEventListener( 'click', ( event ) => {
			if ( isOpen() && ! header.contains( event.target ) ) {
				setOpen( false );
			}
		} );
		const desktop = window.matchMedia( '(min-width: 901px)' );
		const onDesktop = () => {
			if ( desktop.matches && isOpen() ) {
				setOpen( false );
			}
		};
		if ( desktop.addEventListener ) {
			desktop.addEventListener( 'change', onDesktop );
		}
	}

	/* ---------- Navigation: sliding pill + scrollspy ---------- */
	function sameDocumentHash( link ) {
		if ( ! link.hash || link.hash.length < 2 ) {
			return '';
		}
		const url = new URL( link.href, window.location.href );
		if ( url.origin !== window.location.origin || url.pathname.replace( /\/$/, '' ) !== window.location.pathname.replace( /\/$/, '' ) ) {
			return '';
		}
		return decodeURIComponent( url.hash.slice( 1 ) );
	}

	function initNav() {
		const nav = document.querySelector( '[data-nav]' );
		if ( ! nav ) {
			return;
		}
		const pill = nav.querySelector( '.nav-pill' );
		const menus = [ nav ].concat( $$( document, '[data-mobile-menu]' ) );
		const topItems = $$( nav, ':scope > ul > li' );
		let activeLi = null;
		let placed = false;

		const placePill = () => {
			if ( ! pill ) {
				return;
			}
			const li = activeLi || nav.querySelector( ':scope > ul > li.current-menu-item, :scope > ul > li.current-menu-ancestor' );
			const link = li && li.querySelector( ':scope > a' );
			if ( ! link || ! link.offsetWidth ) {
				pill.classList.remove( 'is-visible' );
				return;
			}
			const navRect = nav.getBoundingClientRect();
			const rect = link.getBoundingClientRect();
			if ( ! placed ) {
				pill.style.transition = 'none';
			}
			pill.style.width = rect.width + 'px';
			pill.style.height = rect.height + 'px';
			pill.style.transform = 'translate(' + ( rect.left - navRect.left ) + 'px,' + ( rect.top - navRect.top ) + 'px)';
			pill.classList.add( 'is-visible' );
			if ( ! placed ) {
				placed = true;
				pill.getBoundingClientRect();
				pill.style.transition = '';
			}
		};

		if ( pill && topItems.length ) {
			nav.classList.add( 'has-pill' );
		}

		// Map in-page links to the sections they point at.
		const pairs = [];
		menus.forEach( ( menu ) => {
			$$( menu, 'a' ).forEach( ( link ) => {
				const id = sameDocumentHash( link );
				const section = id && document.getElementById( id );
				if ( section ) {
					pairs.push( { link, section } );
				}
			} );
		} );

		const setActive = ( id ) => {
			activeLi = null;
			menus.forEach( ( menu ) => $$( menu, 'li.is-active' ).forEach( ( li ) => li.classList.remove( 'is-active' ) ) );
			pairs.forEach( ( pair ) => {
				const match = pair.section.id === id;
				pair.link.toggleAttribute( 'aria-current', match );
				if ( match ) {
					pair.link.setAttribute( 'aria-current', 'true' );
					const li = pair.link.closest( 'li' );
					if ( ! li ) {
						return;
					}
					li.classList.add( 'is-active' );
					if ( nav.contains( li ) && li.parentElement.parentElement === nav ) {
						activeLi = li;
					}
				}
			} );
			placePill();
		};

		if ( pairs.length && 'IntersectionObserver' in window ) {
			const sections = Array.from( new Set( pairs.map( ( pair ) => pair.section ) ) );
			const observer = new IntersectionObserver(
				( entries ) => {
					entries.forEach( ( entry ) => {
						if ( entry.isIntersecting ) {
							setActive( entry.target.id );
						}
					} );
				},
				{ rootMargin: '-45% 0px -50% 0px' }
			);
			sections.forEach( ( section ) => observer.observe( section ) );
		}

		placePill();
		window.addEventListener( 'resize', placePill );
		if ( document.fonts && document.fonts.ready ) {
			document.fonts.ready.then( placePill );
		}

		// Hovering a link previews the pill there, then it returns to the active item.
		topItems.forEach( ( li ) => {
			li.addEventListener( 'mouseenter', () => {
				const keep = activeLi;
				activeLi = li;
				placePill();
				activeLi = keep;
			} );
		} );
		nav.addEventListener( 'mouseleave', placePill );
	}

	/* ---------- Scroll progress ---------- */
	function initProgress() {
		const bar = document.querySelector( '[data-scroll-progress]' );
		if ( ! bar ) {
			return;
		}
		let current = 0;
		let target = 0;
		let running = false;
		const step = () => {
			current += ( target - current ) * 0.2;
			if ( Math.abs( target - current ) < 0.0005 || reduceMotion.matches ) {
				current = target;
				running = false;
			}
			bar.style.transform = 'scaleX(' + current.toFixed( 4 ) + ')';
			if ( running ) {
				window.requestAnimationFrame( step );
			}
		};
		const update = () => {
			const max = doc.scrollHeight - window.innerHeight;
			target = max > 0 ? Math.min( 1, Math.max( 0, window.scrollY / max ) ) : 0;
			if ( ! running ) {
				running = true;
				window.requestAnimationFrame( step );
			}
		};
		onScroll( update );
		update();
	}

	/* ---------- Scroll reveal & stagger ---------- */
	let revealObserver = null;

	function initReveal( root ) {
		$$( root, '[data-stagger]' ).forEach( ( group ) => {
			let index = 0;
			Array.prototype.forEach.call( group.children, ( child ) => {
				if ( child.hasAttribute( 'data-reveal-item' ) ) {
					child.style.setProperty( '--i', index++ );
				}
			} );
		} );

		if ( ! doc.classList.contains( 'reveal-ready' ) ) {
			return;
		}

		if ( ! revealObserver ) {
			revealObserver = new IntersectionObserver(
				( entries ) => {
					entries.forEach( ( entry ) => {
						if ( ! entry.isIntersecting ) {
							return;
						}
						const el = entry.target;
						el.classList.add( 'is-visible' );
						revealObserver.unobserve( el );
						if ( el.hasAttribute( 'data-stagger' ) ) {
							const count = el.querySelectorAll( ':scope > [data-reveal-item]' ).length;
							window.setTimeout( () => el.classList.add( 'is-revealed' ), 650 + count * 90 );
						}
					} );
				},
				{ rootMargin: '0px 0px -60px 0px' }
			);
		}

		$$( root, '[data-reveal], [data-stagger]' ).forEach( ( el ) => {
			if ( ! el.classList.contains( 'is-visible' ) ) {
				revealObserver.observe( el );
			}
		} );
		if ( root.matches && root.matches( '[data-reveal], [data-stagger]' ) ) {
			revealObserver.observe( root );
		}
	}

	/* ---------- Spotlight cards ---------- */
	function initSpotlight( root ) {
		$$( root, '[data-spotlight]' ).forEach( ( group ) => {
			if ( ! once( group, 'riazSpot' ) ) {
				return;
			}
			group.addEventListener( 'pointermove', ( event ) => {
				const card = event.target.closest( '.spotlight' );
				if ( ! card || ! group.contains( card ) ) {
					return;
				}
				const rect = card.getBoundingClientRect();
				card.style.setProperty( '--spot-x', event.clientX - rect.left + 'px' );
				card.style.setProperty( '--spot-y', event.clientY - rect.top + 'px' );
			} );
		} );
	}

	/* ---------- Process timeline fill ---------- */
	function initTimeline( root ) {
		$$( root, '[data-timeline]' ).forEach( ( line ) => {
			if ( ! once( line, 'riazTimeline' ) || reduceMotion.matches ) {
				return;
			}
			const bar = line.firstElementChild;
			let current = 0;
			let target = 0;
			let running = false;

			const step = () => {
				current += ( target - current ) * 0.12;
				if ( Math.abs( target - current ) < 0.001 ) {
					current = target;
					running = false;
				}
				bar.style.setProperty( '--progress', current.toFixed( 4 ) );
				if ( running ) {
					window.requestAnimationFrame( step );
				}
			};
			const measure = () => {
				const rect = line.getBoundingClientRect();
				const vh = window.innerHeight;
				// Fill from when the line enters at 85% of the viewport until it reaches 45%.
				const progress = ( vh * 0.85 - rect.top ) / ( vh * 0.4 + rect.height );
				target = Math.max( 0, Math.min( 1, progress ) );
				if ( ! running ) {
					running = true;
					window.requestAnimationFrame( step );
				}
			};
			bar.style.setProperty( '--progress', 0 );
			onScroll( measure );
			measure();
		} );
	}

	/* ---------- FAQ accordion with smooth height ---------- */
	function initAccordion( root ) {
		$$( root, '[data-accordion]' ).forEach( ( details ) => {
			if ( ! once( details, 'riazAccordion' ) ) {
				return;
			}
			const summary = details.querySelector( 'summary' );
			const panel = details.querySelector( '.faq-answer' );
			if ( ! summary || ! panel || ! panel.animate ) {
				return;
			}
			let animation = null;

			summary.addEventListener( 'click', ( event ) => {
				if ( reduceMotion.matches ) {
					return;
				}
				event.preventDefault();
				if ( animation ) {
					animation.cancel();
				}
				if ( details.open && ! details.classList.contains( 'is-closing' ) ) {
					details.classList.add( 'is-closing' );
					animation = panel.animate( [ { height: panel.offsetHeight + 'px' }, { height: '0px' } ], { duration: 320, easing: EASE } );
					animation.onfinish = () => {
						details.open = false;
						details.classList.remove( 'is-closing' );
						animation = null;
					};
					animation.oncancel = () => details.classList.remove( 'is-closing' );
				} else {
					details.classList.remove( 'is-closing' );
					details.open = true;
					animation = panel.animate( [ { height: '0px' }, { height: panel.offsetHeight + 'px' } ], { duration: 380, easing: EASE } );
					animation.onfinish = () => {
						animation = null;
					};
				}
			} );
		} );
	}

	/* ---------- Carousels ---------- */
	function initCarousels( root ) {
		$$( root, '[data-carousel]' ).forEach( ( carousel ) => {
			if ( ! once( carousel, 'riazCarousel' ) ) {
				return;
			}
			const track = carousel.querySelector( '[data-carousel-track]' );
			const controls = carousel.querySelector( '[data-carousel-controls]' );
			const prev = carousel.querySelector( '[data-carousel-prev]' );
			const next = carousel.querySelector( '[data-carousel-next]' );
			if ( ! track || ! controls || ! prev || ! next ) {
				return;
			}
			const update = () => {
				const canPrev = track.scrollLeft > 4;
				const canNext = track.scrollLeft + track.clientWidth < track.scrollWidth - 4;
				controls.hidden = ! canPrev && ! canNext;
				prev.disabled = ! canPrev;
				next.disabled = ! canNext;
			};
			const move = ( direction ) => {
				const slide = track.querySelector( '.carousel-slide' );
				if ( ! slide ) {
					return;
				}
				const gap = parseFloat( window.getComputedStyle( slide.parentElement ).columnGap ) || 0;
				track.scrollBy( {
					left: direction * ( slide.offsetWidth + gap ),
					behavior: reduceMotion.matches ? 'auto' : 'smooth',
				} );
			};
			prev.addEventListener( 'click', () => move( -1 ) );
			next.addEventListener( 'click', () => move( 1 ) );
			track.addEventListener( 'scroll', update, { passive: true } );
			window.addEventListener( 'resize', update );
			update();
			window.setTimeout( update, 400 );
		} );
	}

	/* ---------- YouTube: thumbnail until played ---------- */
	function stopVideo( frame ) {
		const iframe = frame.querySelector( 'iframe' );
		if ( iframe ) {
			iframe.remove();
		}
		frame.classList.remove( 'is-playing' );
	}

	function initVideos( root ) {
		$$( root, '[data-youtube]' ).forEach( ( frame ) => {
			if ( ! once( frame, 'riazVideo' ) ) {
				return;
			}
			const button = frame.querySelector( '.video-play' );
			if ( ! button ) {
				return;
			}
			button.addEventListener( 'click', () => {
				$$( document, '.video-frame.is-playing' ).forEach( ( other ) => other !== frame && stopVideo( other ) );
				const iframe = document.createElement( 'iframe' );
				iframe.src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent( frame.dataset.youtube ) + '?autoplay=1&playsinline=1&rel=0';
				iframe.title = frame.dataset.title || 'YouTube video';
				iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
				iframe.referrerPolicy = 'strict-origin-when-cross-origin';
				iframe.allowFullscreen = true;
				frame.classList.add( 'is-playing' );
				frame.appendChild( iframe );
				iframe.focus();
			} );
		} );
	}

	/* ---------- Portfolio filter ---------- */
	function initFilters( root ) {
		$$( root, '[data-filter-group]' ).forEach( ( group ) => {
			if ( ! once( group, 'riazFilter' ) ) {
				return;
			}
			const scope = group.closest( 'section' ) || document;
			const grid = scope.querySelector( '[data-filter-grid]' );
			const status = scope.querySelector( '[data-filter-status]' );
			if ( ! grid ) {
				return;
			}
			group.addEventListener( 'click', ( event ) => {
				const button = event.target.closest( 'button[data-filter]' );
				if ( ! button ) {
					return;
				}
				const filter = button.dataset.filter;
				$$( group, 'button[data-filter]' ).forEach( ( item ) => item.setAttribute( 'aria-pressed', item === button ? 'true' : 'false' ) );

				let shown = 0;
				Array.prototype.forEach.call( grid.children, ( card ) => {
					const terms = ( card.dataset.terms || '' ).split( ' ' );
					const match = '*' === filter || -1 !== terms.indexOf( filter );
					card.classList.toggle( 'is-filtered-out', ! match );
					if ( match ) {
						if ( card.animate && ! reduceMotion.matches ) {
							card.animate(
								[
									{ opacity: 0, transform: 'translateY(18px) scale(0.98)' },
									{ opacity: 1, transform: 'none' },
								],
								{ duration: 480, easing: EASE, delay: shown * 50, fill: 'backwards' }
							);
						}
						shown++;
					}
				} );
				if ( status && status.dataset.template ) {
					status.textContent = status.dataset.template.replace( '%d', shown );
				}
			} );
		} );
	}

	/* ---------- Count-up statistics ---------- */
	function countUp( el ) {
		const raw = String( el.dataset.count );
		const target = parseFloat( raw );
		if ( isNaN( target ) ) {
			return;
		}
		const decimals = ( raw.split( '.' )[ 1 ] || '' ).length;
		const original = el.dataset.final || el.textContent;
		let format;
		try {
			format = new Intl.NumberFormat( doc.lang || undefined, { minimumFractionDigits: decimals, maximumFractionDigits: decimals } );
		} catch ( error ) {
			format = { format: ( n ) => n.toFixed( decimals ) };
		}
		const start = window.performance.now();
		const duration = 1600;
		const frame = ( now ) => {
			const t = Math.min( 1, ( now - start ) / duration );
			const eased = 1 - Math.pow( 1 - t, 4 );
			el.textContent = format.format( target * eased );
			if ( t < 1 ) {
				window.requestAnimationFrame( frame );
			} else {
				el.textContent = original;
			}
		};
		window.requestAnimationFrame( frame );
	}

	function initCounters( root ) {
		const counters = $$( root, '[data-count]' );
		if ( ! counters.length || reduceMotion.matches || ! ( 'IntersectionObserver' in window ) ) {
			return;
		}
		const observer = new IntersectionObserver(
			( entries ) => {
				entries.forEach( ( entry ) => {
					if ( entry.isIntersecting ) {
						observer.unobserve( entry.target );
						countUp( entry.target );
					}
				} );
			},
			{ threshold: 0.5 }
		);
		counters.forEach( ( el ) => {
			if ( once( el, 'riazCount' ) ) {
				el.dataset.final = el.textContent;
				observer.observe( el );
			}
		} );
	}

	/* ---------- Copy-link buttons ---------- */
	function initCopyLinks( root ) {
		$$( root, '[data-copy-link]' ).forEach( ( button ) => {
			if ( ! once( button, 'riazCopy' ) ) {
				return;
			}
			button.addEventListener( 'click', () => {
				if ( ! navigator.clipboard ) {
					return;
				}
				navigator.clipboard.writeText( button.dataset.copyLink ).then( () => {
					button.classList.add( 'is-copied' );
					announce( button.dataset.copiedLabel || 'Copied' );
					window.setTimeout( () => button.classList.remove( 'is-copied' ), 2000 );
				} );
			} );
		} );
	}

	/* ---------- In-page anchors that work from any page ---------- */
	function initAnchors() {
		document.addEventListener( 'click', ( event ) => {
			if ( event.defaultPrevented || 0 !== event.button || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey ) {
				return;
			}
			const link = event.target.closest( 'a[href*="#"]' );
			if ( ! link || link.target === '_blank' ) {
				return;
			}
			const url = new URL( link.href, window.location.href );
			if ( url.origin !== window.location.origin || url.pathname !== window.location.pathname || url.search !== window.location.search ) {
				return;
			}
			const id = decodeURIComponent( url.hash.slice( 1 ) );
			if ( ! id ) {
				return;
			}
			const behavior = reduceMotion.matches ? 'auto' : 'smooth';
			const target = document.getElementById( id );

			if ( ! target ) {
				event.preventDefault();
				if ( 'top' === id ) {
					window.scrollTo( { top: 0, behavior } );
					return;
				}
				// The section lives on the homepage.
				const home = body.dataset.homeUrl;
				if ( home ) {
					window.location.href = home + '#' + encodeURIComponent( id );
				}
				return;
			}

			event.preventDefault();
			target.scrollIntoView( { behavior, block: 'start' } );
			if ( window.history.pushState ) {
				window.history.pushState( null, '', '#' + id );
			}
			if ( 'main' === id || ! target.matches( 'a, button, input, textarea, select, summary, [tabindex]' ) ) {
				target.setAttribute( 'tabindex', '-1' );
			}
			target.focus( { preventScroll: true } );
		} );
	}

	/* ---------- Custom cursor ---------- */
	function initCursor() {
		if ( ! body.classList.contains( 'rp-cursor' ) ) {
			return;
		}
		const query = window.matchMedia( '(pointer: fine) and (prefers-reduced-motion: no-preference)' );
		if ( ! query.matches ) {
			return;
		}

		const INTERACTIVE = 'a, button, summary, label, [role="button"], input, textarea, select';
		const DARK = '.hero-section, .approach-section, .cta-card, .site-footer, .service-card-featured, .pricing-card-featured, .about-photo, .video-frame, .page-banner, .project-media, .team-photo';

		const ring = document.createElement( 'div' );
		const dot = document.createElement( 'div' );
		ring.className = 'cursor-ring';
		dot.className = 'cursor-dot';
		ring.setAttribute( 'aria-hidden', 'true' );
		dot.setAttribute( 'aria-hidden', 'true' );
		body.appendChild( ring );
		body.appendChild( dot );
		doc.classList.add( 'has-custom-cursor' );

		let x = -100;
		let y = -100;
		let rx = -100;
		let ry = -100;
		let vx = 0;
		let vy = 0;
		let frame = null;
		let visible = false;

		const setVisible = ( state ) => {
			visible = state;
			ring.classList.toggle( 'is-visible', state );
			dot.classList.toggle( 'is-visible', state );
		};
		const setTone = ( target ) => {
			const el = target && target.closest ? target : null;
			const hovering = !! ( el && el.closest( INTERACTIVE ) );
			const dark = !! ( el && el.closest( DARK ) );
			[ ring, dot ].forEach( ( node ) => {
				node.classList.toggle( 'is-hovering', hovering );
				node.classList.toggle( 'is-dark', dark );
			} );
		};
		const loop = () => {
			// Spring the ring towards the pointer; the dot follows exactly.
			vx = ( vx + ( x - rx ) * 0.2 ) * 0.68;
			vy = ( vy + ( y - ry ) * 0.2 ) * 0.68;
			rx += vx;
			ry += vy;
			ring.style.transform = 'translate3d(' + rx + 'px,' + ry + 'px,0)';
			dot.style.transform = 'translate3d(' + x + 'px,' + y + 'px,0)';
			if ( Math.abs( x - rx ) > 0.1 || Math.abs( y - ry ) > 0.1 || Math.abs( vx ) > 0.1 || Math.abs( vy ) > 0.1 ) {
				frame = window.requestAnimationFrame( loop );
			} else {
				frame = null;
			}
		};

		window.addEventListener(
			'pointermove',
			( event ) => {
				if ( 'mouse' !== event.pointerType ) {
					return;
				}
				x = event.clientX;
				y = event.clientY;
				if ( ! visible ) {
					rx = x;
					ry = y;
					setVisible( true );
				}
				setTone( event.target );
				if ( ! frame ) {
					frame = window.requestAnimationFrame( loop );
				}
			},
			{ passive: true }
		);
		window.addEventListener( 'pointerdown', () => ring.classList.add( 'is-pressed' ) );
		window.addEventListener( 'pointerup', () => ring.classList.remove( 'is-pressed' ) );
		window.addEventListener( 'pointerover', ( event ) => {
			if ( event.target instanceof window.HTMLIFrameElement ) {
				setVisible( false );
			}
		} );
		doc.addEventListener( 'pointerleave', () => setVisible( false ) );
		window.addEventListener( 'blur', () => setVisible( false ) );
		onScroll( () => {
			if ( visible ) {
				setTone( document.elementFromPoint( x, y ) );
			}
		} );
	}

	/* ---------- Boot ---------- */
	function init( root ) {
		const scope = root || document;
		initReveal( scope );
		initSpotlight( scope );
		initTimeline( scope );
		initAccordion( scope );
		initCarousels( scope );
		initVideos( scope );
		initFilters( scope );
		initCounters( scope );
		initCopyLinks( scope );
	}

	initHeader();
	initNav();
	initProgress();
	initAnchors();
	initCursor();
	init( document );

	// Public hook for the Customizer preview and page builders.
	window.RiazTheme = { init };

	// Elementor: initialise theme sections dropped into the editor preview.
	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', () => {
			if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
				window.elementorFrontend.hooks.addAction( 'frontend/element_ready/riaz-section.default', ( $scope ) => init( $scope[ 0 ] ) );
			}
		} );
	}
} )();
