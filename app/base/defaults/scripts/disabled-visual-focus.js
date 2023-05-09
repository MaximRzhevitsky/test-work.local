import liveDom from './libs/live-dom';

const disabledVisualFocus = 'disabled-visual-focus';
// Don't use it here input:not(:where(...))
// Since older browsers do not support where and break everything :(
const excludedTypes = [
	'button',
	'checkbox',
	'file',
	'hidden',
	'image',
	'radio',
	'range',
	'reset',
	'submit',
];

let selector = 'textarea,input';
excludedTypes.forEach( ( excludedType ) => {
	selector += `:not([type="${ excludedType }"])`;
} );

liveDom( selector ).init( function () {
	/**
	 * @type {HTMLInputElement|HTMLTextAreaElement}
	 */
	const input = this;

	input.addEventListener( 'mousedown', () => {
		if ( input !== document.activeElement ) {
			input.classList.add( disabledVisualFocus );
		}
	} );

	input.addEventListener( 'blur', () => {
		input.classList.remove( disabledVisualFocus );
	} );
} );
