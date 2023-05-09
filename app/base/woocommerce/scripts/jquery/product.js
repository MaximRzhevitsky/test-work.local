import { toggleFocus } from '../../../defaults/scripts/libs/toggle-focus';

jQuery( ( $ ) => {
	const $writeReviewBtn = $( '.write-review .btn' );
	const $reviewWrapper = $( '#review_form_wrapper' );

	if ( $writeReviewBtn.length && $reviewWrapper.length ) {
		$writeReviewBtn.on( 'click', () => $reviewWrapper.toggle( 250 ) );
	}

	$( '.comment-form p[class*=comment-form-]' ).each( function () {
		toggleFocus( `.${ $( this ).attr( 'class' ) }` );
	} );
} );
