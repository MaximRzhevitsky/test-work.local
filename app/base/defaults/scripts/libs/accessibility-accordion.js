export default function accessibilityAccordion( accordionNode ) {
	const buttonEl = accordionNode.querySelector( 'button[aria-expanded]' );
	const controlsId = buttonEl.getAttribute( 'aria-controls' );
	const contentEl = document.getElementById( controlsId );

	let state = buttonEl.getAttribute( 'aria-expanded' ) === 'true';

	function toggle( newState ) {
		if ( newState === state ) {
			return;
		}
		state = newState;
		buttonEl.setAttribute( 'aria-expanded', `${ state }` );
		if ( state ) {
			contentEl.removeAttribute( 'hidden' );
			accordionNode.classList.add( 'accordion_active' );
		} else {
			contentEl.setAttribute( 'hidden', '' );
			accordionNode.classList.remove( 'accordion_active' );
		}
	}

	function onButtonClick() {
		toggle( ! state );
	}

	function init() {
		buttonEl.addEventListener( 'click', onButtonClick );
	}

	init();
}
