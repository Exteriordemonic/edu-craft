import { store } from '@wordpress/interactivity';

/**
 * Escapes text for safe HTML interpolation (card markup is built client-side after REST fetch).
 *
 * @param {unknown} value
 * @returns {string}
 */
function escapeHtml( value ) {
	return String( value ?? '' )
		.replace( /&/g, '&amp;' )
		.replace( /</g, '&lt;' )
		.replace( />/g, '&gt;' )
		.replace( /"/g, '&quot;' );
}

/**
 * Renders archive cards HTML from REST/SSR-shaped item objects.
 * Keep markup aligned with edu_craft_render_case_study_archive_card() in block-helpers.php.
 *
 * @param {Array<Record<string, unknown>>} items
 * @returns {string}
 */
function renderCardsHtml( items ) {
	if ( ! Array.isArray( items ) || items.length === 0 ) {
		return '';
	}

	return items
		.map( ( item ) => {
			const title = escapeHtml( item.title );
			const permalink = escapeHtml( item.permalink );
			const excerpt = escapeHtml( item.excerpt );
			const thumb = item.thumbnail && typeof item.thumbnail === 'object' ? item.thumbnail : null;
			const thumbSrc = thumb && thumb.src ? escapeHtml( thumb.src ) : '';
			const thumbAlt = thumb && thumb.alt ? escapeHtml( thumb.alt ) : title;
			const industries = Array.isArray( item.industries ) ? item.industries : [];

			const imageSection =
				thumbSrc !== ''
					? `<a href="${ permalink }" class="ratio ratio-16x9 edu-craft-csa-card__image-link bg-light"><img src="${ thumbSrc }" alt="${ thumbAlt }" class="card-img-top object-fit-cover h-100 rounded-top" loading="lazy" /></a>`
					: '';

			const industriesHtml = industries
				.filter( ( ind ) => ind && typeof ind === 'object' )
				.map( ( ind ) => {
					const name = escapeHtml( ind.name );
					const link = ind.link ? escapeHtml( ind.link ) : '';
					if ( link !== '' ) {
						return `<a href="${ link }" class="cs-industry-badge" tabindex="-1">${ name }</a>`;
					}
					return `<span class="cs-industry-badge">${ name }</span>`;
				} )
				.join( '' );

			const excerptBlock =
				excerpt !== '' ? `<p class="card-text text-secondary small">${ excerpt }</p>` : '';

			const badgesBlock =
				industriesHtml !== ''
					? `<div class="d-flex flex-wrap gap-2 mt-auto pt-2">${ industriesHtml }</div>`
					: '';

			return `<div class="col-12 col-md-6 col-lg-4"><article class="card h-100 shadow-sm border-0 edu-craft-csa-card">${ imageSection }<div class="card-body d-flex flex-column"><h3 class="h5 card-title"><a href="${ permalink }" class="stretched-link text-decoration-none text-dark">${ title }</a></h3>${ excerptBlock }${ badgesBlock }</div></article></div>`;
		} )
		.join( '' );
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
					grid.innerHTML = renderCardsHtml( items );
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
