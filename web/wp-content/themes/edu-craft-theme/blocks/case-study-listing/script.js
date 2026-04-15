import { getContext, store } from '@wordpress/interactivity';

const CARD_TEMPLATE_ID = 'edu-craft-csa-card-template';



/**
 * Fill single card
 */
function fillCard(col, item = {}) {
	const {
		title = '',
		permalink = '#',
		excerpt = '',
		thumbnail,
		industries = [],
	} = item;

	const thumbSrc = thumbnail?.src || '';
	const thumbAlt = thumbnail?.alt || title;

	const imageLink = col.querySelector('.js-csa-card-image-link');
	if (imageLink) {
		imageLink.href = permalink;

		if (thumbSrc) {
			imageLink.classList.remove('d-none');

			let img = col.querySelector('.js-csa-card-img');
			if (!img) {
				img = document.createElement('img');
				img.className =
					'card-img-top object-fit-cover h-100 rounded-top js-csa-card-img';
				img.loading = 'lazy';
				img.decoding = 'async';
				imageLink.append(img);
			}

			img.src = thumbSrc;
			img.alt = thumbAlt;
		} else {
			imageLink.classList.add('d-none');
			imageLink.innerHTML = '';
		}
	}

	const titleLink = col.querySelector('.js-csa-card-title-link');
	if (titleLink) {
		titleLink.href = permalink;
		titleLink.textContent = title;
	}

	const excerptEl = col.querySelector('.js-csa-card-excerpt');
	if (excerptEl) {
		excerptEl.textContent = excerpt;
		excerptEl.classList.toggle('d-none', !excerpt);
	}

	const badges = col.querySelector('.js-csa-card-badges');
	if (badges) {
		badges.innerHTML = '';

		if (!industries.length) {
			badges.classList.add('d-none');
			return;
		}

		badges.classList.remove('d-none');

		industries.forEach(({ name = '', link = '' }) => {
			if (!name) return;

			const el = document.createElement(link ? 'a' : 'span');

			if (link) {
				el.href = link;
				el.tabIndex = -1;
			}

			el.className = 'cs-industry-badge js-csa-card-badge';
			el.textContent = name;

			badges.append(el);
		});
	}
}

/**
 * Render cards from template
 */
function renderCards(items = []) {
	if (!items.length) return '';

	const tpl = document.getElementById(CARD_TEMPLATE_ID);
	if (!tpl?.content) return '';

	const holder = document.createElement('div');

	items.forEach((item) => {
		const fragment = tpl.content.cloneNode(true);
		const col = fragment.querySelector('.js-csa-card-col');

		if (!col) return;

		fillCard(col, item);
		holder.append(col);
	});

	return holder.innerHTML;
}

function syncIndustryUrlQuery(slug) {
	const param = archiveState.queryParam || 'industry';
	const url = new URL(window.location.href);
	if (slug) {
		url.searchParams.set(param, slug);
	} else {
		url.searchParams.delete(param);
	}
	window.history.replaceState({}, '', `${url.pathname}${url.search}${url.hash}`);
}

const { state: archiveState } = store('eduCraftCaseStudyArchive', {
	callbacks: {
		isIndustryActive() {
			const context = getContext();
			return archiveState.activeIndustry === context.slug;
		},

		hideEmptyArchiveMessage() {
			return (
				archiveState.invalidIndustry ||
				archiveState.items.length > 0 ||
				archiveState.isLoading
			);
		},
	},

	actions: {
		*selectIndustry() {
			const context = getContext();
			const slug = context.slug;

			archiveState.activeIndustry = slug;
			archiveState.isLoading = true;

			const restBase = archiveState.restUrl;
			if (!restBase) {
				archiveState.isLoading = false;
				return;
			}

			const requestUrl = new URL(restBase, window.location.origin);
			if (slug) {
				requestUrl.searchParams.set('industry', slug);
			} else {
				requestUrl.searchParams.delete('industry');
			}
			requestUrl.searchParams.set('per_page', '20');

			try {
				const response = yield fetch(requestUrl.toString(), {
					method: 'GET',
					credentials: 'same-origin',
					headers: {
						Accept: 'application/json',
						...(archiveState.nonce
							? { 'X-WP-Nonce': archiveState.nonce }
							: {}),
					},
				});

				if (!response.ok) {
					throw new Error(`HTTP ${response.status}`);
				}

				const data = yield response.json();
				const items = Array.isArray(data.items) ? data.items : [];

				archiveState.items = items;
				archiveState.invalidIndustry = Boolean(data.invalid_industry);

				const grid = document.getElementById('edu-craft-csa-grid');
				if (grid) {
					grid.innerHTML = renderCards(items);
				}

				syncIndustryUrlQuery(slug);
			} catch {
				archiveState.invalidIndustry = false;
			} finally {
				archiveState.isLoading = false;
			}
		},
	},
});