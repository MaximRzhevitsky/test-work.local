export default function accessibilitySetModalFocus(
	event,
	panel,
	closeButton
) {
	const { shiftKey } = event;
	const elements = panel.querySelectorAll(
		'input, a:not(.menu-cart-link):not(.menu-cart__close), button'
	);

	const tabKey = event.keyCode === 9;
	const escKey = event.keyCode === 27;
	const lastEl = elements.item( elements.length - 1 );
	const firstEl = closeButton;
	const activeEl = document.activeElement;

	if ( escKey ) {
		event.preventDefault();
		closeButton.click();
	} else if ( ! shiftKey && tabKey && lastEl === activeEl ) {
		event.preventDefault();
		firstEl.focus();
	} else if ( ! shiftKey && tabKey && firstEl === activeEl ) {
		event.preventDefault();
		elements.item( 0 ).focus();
	} else if ( shiftKey && tabKey && firstEl === activeEl ) {
		event.preventDefault();
		lastEl.focus();
	} else if ( shiftKey && tabKey && firstEl === activeEl ) {
		event.preventDefault();
		lastEl.focus();
	} else if ( shiftKey && tabKey && elements.item( 0 ) === activeEl ) {
		event.preventDefault();
		firstEl.focus();
	} else if ( tabKey && firstEl === lastEl ) {
		event.preventDefault();
	}
}
