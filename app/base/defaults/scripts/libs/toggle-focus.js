import liveDom from './live-dom';

function toggle( inputElement, wrapperElement, className, force = false ) {
	setTimeout( () => {
		wrapperElement.classList.toggle(
			`${ className }_in-focus-or-has-value`,
			force ||
				!! inputElement.value ||
				!! inputElement.getAttribute( 'placeholder' )
		);
	} );
}

function toggleForSelect2( select2, wrapperElement, className, force = false ) {
	setTimeout( () => {
		wrapperElement.classList.toggle(
			`${ className }_in-focus-or-has-value`,
			force ||
				select2
					.querySelector( '.select2-selection__rendered' )
					.hasAttribute( 'title' )
		);
	} );

	const observer = new MutationObserver( function ( mutations ) {
		mutations.forEach( function ( mutation ) {
			wrapperElement.classList.toggle(
				`${ className }_in-focus-or-has-value`,
				force || mutation.target.hasAttribute( 'title' )
			);
		} );
	} );

	const config = { attributes: true, childList: false, characterData: false };
	observer.observe(
		select2.querySelector( '.select2-selection__rendered' ),
		config
	);
}

export function toggleFocus( wrapper, parent = '' ) {
	liveDom( parent + ' ' + wrapper ).init( function () {
		let className = wrapper;
		if ( wrapper[ 0 ] === '.' || wrapper[ 0 ] === '#' ) {
			className = className.slice( 1 );
		}
		const wrapperElement = this;
		const inputElement = wrapperElement.querySelector(
			'input, select, textarea'
		);

		const select2Element = wrapperElement.querySelector( '.select2' );

		if ( inputElement ) {
			toggle( inputElement, wrapperElement, className );

			inputElement.addEventListener( 'focus', () =>
				toggle( inputElement, wrapperElement, className, true )
			);
			inputElement.addEventListener( 'blur', () =>
				toggle( inputElement, wrapperElement, className )
			);
		}

		if ( select2Element ) {
			toggleForSelect2( select2Element, wrapperElement, className );
		}
	} );
}
