import { store, getContext, getElement } from '@wordpress/interactivity';

store('edu-craft/case-study-gallery', {
	actions: {
		open() {
			const { ref } = getElement();
			const context = getContext();
			const images = Array.isArray(context.images) ? context.images : [];

			if (!images.length) {
				return;
			}

			const parsedIndex = Number.parseInt(ref?.dataset?.index ?? '', 10);
			const nextIndex = Number.isInteger(parsedIndex) ? parsedIndex : 0;
			const boundedIndex = Math.min(Math.max(nextIndex, 0), images.length - 1);

			context.current = boundedIndex;
			context.isOpen = true;
			context.counter = `${boundedIndex + 1} / ${images.length}`;
			syncLightboxImage(context);

			document.addEventListener('keydown', handleKeydown);
		},

		close() {
			const context = getContext();
			context.isOpen = false;
			document.removeEventListener('keydown', handleKeydown);
		},

		next() {
			const context = getContext();

			if (!context.images.length) {
				return;
			}

			context.current = (context.current + 1) % context.images.length;
			context.counter = `${context.current + 1} / ${context.images.length}`;
			syncLightboxImage(context);
		},

		prev() {
			const context = getContext();

			if (!context.images.length) {
				return;
			}

			context.current = (context.current - 1 + context.images.length) % context.images.length;
			context.counter = `${context.current + 1} / ${context.images.length}`;
			syncLightboxImage(context);
		},

		onKeydown(event) {
			// Fallback for cases when key events are scoped to the lightbox element.
			handleKeydown(event);
		},
	},
});

/**
 * Bindings only support dot paths (e.g. context.lightboxSrc), not bracket indexing.
 */
function syncLightboxImage(context) {
	const images = Array.isArray(context.images) ? context.images : [];
	if (!images.length) {
		context.lightboxSrc = '';
		context.lightboxAlt = '';
		return;
	}

	const rawIndex = Number(context.current);
	const index = Number.isInteger(rawIndex)
		? Math.min(Math.max(rawIndex, 0), images.length - 1)
		: 0;
	const item = images[index];

	context.lightboxSrc = item?.src ?? '';
	context.lightboxAlt = item?.alt ?? '';
}

function handleKeydown(event) {
	if (event.key === 'Escape') {
		document.querySelector('.cs-lightbox__close')?.click();
	}

	if (event.key === 'ArrowRight') {
		document.querySelector('.cs-lightbox__nav--next')?.click();
	}

	if (event.key === 'ArrowLeft') {
		document.querySelector('.cs-lightbox__nav--prev')?.click();
	}
}
