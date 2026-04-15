/******/ (function() { // webpackBootstrap
/*!*******************************!*\
  !*** ./src/js/woocommerce.js ***!
  \*******************************/
const cartToggleButton = document.querySelector('.js-edu-craft-cart-toggle');
const cartPanel = document.querySelector('.js-edu-craft-cart-panel');

if (cartToggleButton && cartPanel) {
	cartToggleButton.addEventListener('click', () => {
		const isOpen = cartPanel.getAttribute('aria-hidden') === 'false';
		cartPanel.setAttribute('aria-hidden', isOpen ? 'true' : 'false');
		cartToggleButton.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
	});
}

/******/ })()
;
//# sourceMappingURL=woocommerce.js.map