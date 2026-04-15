import { store } from '@wordpress/interactivity';

const CARD_TEMPLATE_ID = 'edu-craft-csa-card-template';

/**
 * Fills a cloned card column from a REST/SSR-shaped item.
 * Markup must match components/case-study-card.php (js-* hooks only).
 *
 * @param {HTMLElement} col
 * @param {Record<string, unknown>} item
 */
function fillCardFromItem( col, item ) {
	const title = String( item.title ?? '' );
	const permalink = String( item.permalink ?? '#' );
	const excerpt = String( item.excerpt ?? '' );
	const thumb = item.thumbnail && typeof item.thumbnail === 'object' ? item.thumbnail : null;
	const thumbSrcRaw = thumb && thumb.src ? String( thumb.src ) : '';
	const thumbAlt = thumb && thumb.alt ? String( thumb.alt ) : title;
	const industries = Array.isArray( item.industries ) ? item.industries : [];

	const imageLink = col.querySelector( '.js-csa-card-image-link' );
	if ( imageLink instanceof HTMLAnchorElement ) {
		imageLink.href = permalink;
		if ( thumbSrcRaw !== '' ) {
			imageLink.classList.remove( 'd-none' );
			let img = col.querySelector( '.js-csa-card-img' );
			if ( ! ( img instanceof HTMLImageElement ) ) {
				img = document.createElement( 'img' );
				img.className = 'card-img-top object-fit-cover h-100 rounded-top js-csa-card-img';
				img.loading = 'lazy';
				img.decoding = 'async';
				imageLink.appendChild( img );
			}
			img.src = thumbSrcRaw;
			img.alt = thumbAlt;
		} else {
			imageLink.classList.add( 'd-none' );
			imageLink.replaceChildren();
		}
	}

	const titleLink = col.querySelector( '.js-csa-card-title-link' );
	if ( titleLink instanceof HTMLAnchorElement ) {
		titleLink.href = permalink;
		titleLink.textContent = title;
	}

	const excerptEl = col.querySelector( '.js-csa-card-excerpt' );
	if ( excerptEl instanceof HTMLElement ) {
		if ( excerpt !== '' ) {
			excerptEl.classList.remove( 'd-none' );
			excerptEl.textContent = excerpt;
		} else {
			excerptEl.classList.add( 'd-none' );
			excerptEl.textContent = '';
		}
	}

	const badges = col.querySelector( '.js-csa-card-badges' );
	if ( badges instanceof HTMLElement ) {
		badges.replaceChildren();
		if ( industries.length === 0 ) {
			badges.classList.add( 'd-none' );
		} else {
			badges.classList.remove( 'd-none' );
			for ( const ind of industries ) {
				if ( ! ind || typeof ind !== 'object' ) {
					continue;
				}
				const indObj = /** @type {Record<string, unknown>} */ ( ind );
				const name = indObj.name != null ? String( indObj.name ) : '';
				if ( name === '' ) {
					continue;
				}
				const link = indObj.link != null ? String( indObj.link ) : '';
				if ( link !== '' ) {
					const a = document.createElement( 'a' );
					a.href = link;
					a.className = 'cs-industry-badge js-csa-card-badge';
					a.tabIndex = -1;
					a.textContent = name;
					badges.append( a );
				} else {
					const span = document.createElement( 'span' );
					span.className = 'cs-industry-badge js-csa-card-badge';
					span.textContent = name;
					badges.append( span );
				}
			}
		}
	}
}

/**
 * @param {Array<Record<string, unknown>>} items
 * @returns {string}
 */
function renderCardsFromTemplate( items ) {
	if ( ! Array.isArray( items ) || items.length === 0 ) {
		return '';
	}

	const tpl = document.getElementById( CARD_TEMPLATE_ID );
	if ( ! tpl || ! tpl.content ) {
		return '';
	}

	const holder = document.createElement( 'div' );
	for ( const item of items ) {
		const fragment = tpl.content.cloneNode( true );
		const col = fragment.querySelector( '.js-csa-card-col' );
		if ( ! ( col instanceof HTMLElement ) ) {
			continue;
		}
		fillCardFromItem( col, item );
		holder.appendChild( col );
	}
	return holder.innerHTML;
}

const { state } = store( 'eduCraftCaseStudyArchive', {
	state: {
		items: [],
		activeIndustry: '',
		invalidIndustry: false,
		isLoading: false,
		restUrl: '',
		nonce: '',
		archiveUrl: '',
		terms: [],
		queryParam: 'industry',
	},
	actions: {
		async selectIndustry( { event } ) {
			const raw = event.currentTarget.getAttribute( 'data-wp-context' );
			let slug = '';
			try {
				const ctx = raw ? JSON.parse( raw ) : {};
				slug = typeof ctx.slug === 'string' ? ctx.slug : '';
			} catch {
				slug = '';
			}

			state.isLoading = true;
			state.invalidIndustry = false;

			try {
				const url = new URL( state.restUrl, window.location.origin );
				if ( slug ) {
					url.searchParams.set( 'industry', slug );
				} else {
					url.searchParams.delete( 'industry' );
				}

				const res = await fetch( url.toString(), {
					method: 'GET',
					credentials: 'same-origin',
					headers: {
						'X-WP-Nonce': state.nonce,
						Accept: 'application/json',
					},
				} );

				if ( ! res.ok ) {
					throw new Error( 'Request failed' );
				}

				const data = await res.json();
				const items = Array.isArray( data.items ) ? data.items : [];
				state.items = items;
				state.activeIndustry = slug;
				state.invalidIndustry = !! data.invalid_industry;

				const archiveTarget = new URL( state.archiveUrl, window.location.href );
				const param = state.queryParam || 'industry';
				if ( slug ) {
					archiveTarget.searchParams.set( param, slug );
				} else {
					archiveTarget.searchParams.delete( param );
				}
				window.history.replaceState( {}, '', archiveTarget.toString() );

				const grid = document.getElementById( 'edu-craft-csa-grid' );
				if ( grid ) {
					grid.innerHTML = renderCardsFromTemplate(
						/** @type {Array<Record<string, unknown>>} */ ( items )
					);
				}
			} catch {
				state.invalidIndustry = false;
				state.items = [];
				const grid = document.getElementById( 'edu-craft-csa-grid' );
				if ( grid ) {
					grid.innerHTML = '';
				}
			} finally {
				state.isLoading = false;
			}
		},
	},
} );
