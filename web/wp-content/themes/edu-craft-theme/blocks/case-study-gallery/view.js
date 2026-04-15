import { store, getContext, getElement } from '@wordpress/interactivity';

const LIGHTBOX_SELECTOR = '[data-wp-interactive="edu-craft/case-study-gallery"]';

store('edu-craft/case-study-gallery', {
	actions: {
		open() {
			const buttonContext = getContext();
			const { ref } = getElement();
			const section = ref.closest(LIGHTBOX_SELECTOR);

			if (!section) {
				return;
			}

			const rootContext = getContext(section);
			const nextIndex = Number.isInteger(buttonContext.index) ? buttonContext.index : 0;

			rootContext.current = nextIndex;
			rootContext.isOpen = true;
			rootContext.counter = `${nextIndex + 1} / ${rootContext.images.length}`;

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
		},

		prev() {
			const context = getContext();

			if (!context.images.length) {
				return;
			}

			context.current = (context.current - 1 + context.images.length) % context.images.length;
			context.counter = `${context.current + 1} / ${context.images.length}`;
		},

		onKeydown(event) {
			// Fallback for cases when key events are scoped to the lightbox element.
			handleKeydown(event);
		},
	},
});

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
