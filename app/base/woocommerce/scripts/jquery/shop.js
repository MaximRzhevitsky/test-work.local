jQuery( ( $ ) => {
	const $wpfCheckboxes = $( '.wpfCheckbox input[type="checkbox"]' );
	const $wpfCheckboxItems = $( '.wpfFilterVerScroll' );
	const $wpfPriceInputs = $( '.filters .wpfPriceInputs' );
	const $wpfClearButton = $( '.filters .wpfClearButton' );
	const $wpfFilterWrapper = $( '.filters .wpfFilterWrapper' );
	const $filterManageSelect = $( '.filters .filters-manage select' );

	function toggleClearBtn() {
		const $clearBtn = $( '.filters .wpfFilterButtons' );
		const isMarked = $wpfFilterWrapper
			.find( 'input[type="checkbox"]' )
			.is( ':checked' );

		if ( isMarked ) {
			$clearBtn.css( 'display', 'block' );
		} else {
			$clearBtn.css( 'display', 'none' );
		}
	}

	toggleClearBtn();

	function closeFilter( filter ) {
		filter.find( '.wpfFilterTitle' ).trigger( 'click' );
	}

	function collapseAllFilters() {
		$wpfFilterWrapper.each( function () {
			if ( $( this ).find( '.wpfFilterContent:not(.wpfHide)' ).length ) {
				closeFilter( $( this ) );
			}
		} );
	}

	function expandAllFilters() {
		$wpfFilterWrapper.each( function () {
			if ( $( this ).find( '.wpfFilterContent.wpfHide' ).length ) {
				closeFilter( $( this ) );
			}
		} );
	}

	function expandMarkedFilters() {
		$wpfFilterWrapper.each( function () {
			const isMarked = $( this )
				.find( 'input[type="checkbox"]' )
				.is( ':checked' );
			const isHide = !! $( this ).find( '.wpfFilterContent.wpfHide' )
				.length;
			if ( ( isMarked && isHide ) || ( ! isMarked && ! isHide ) ) {
				closeFilter( $( this ) );
			}
		} );
	}

	if ( $wpfFilterWrapper && $wpfFilterWrapper.length ) {
		$filterManageSelect.on( 'change', () => {
			const value = $filterManageSelect.val();
			if ( value === 'collapse_all' ) {
				collapseAllFilters();
			} else if ( value === 'expand_all' ) {
				expandAllFilters();
			} else if ( value === 'expand_marked' ) {
				expandMarkedFilters();
			}
		} );
	}

	$wpfCheckboxes.each( function () {
		$( this ).on( 'change', function () {
			if ( $( this ).is( ':checked' ) ) {
				$( this )
					.closest( '.wpfLiLabel' )
					.find( '.wpfDisplay' )
					.addClass( 'active' );
			} else {
				$( this )
					.closest( '.wpfLiLabel' )
					.find( '.wpfDisplay' )
					.removeClass( 'active' );

				if ( $filterManageSelect.val() === 'expand_marked' ) {
					expandMarkedFilters();
				}
			}
			toggleClearBtn();
		} );
	} );

	if ( $wpfPriceInputs ) {
		$wpfPriceInputs.append(
			'<button class="btn" type="button">Ok</button>'
		);
	}

	if ( $wpfCheckboxItems && $wpfCheckboxItems.length ) {
		$wpfCheckboxItems.each( function () {
			const $wpfCheckboxItemsLength = $( this ).find( 'li' ).length;
			if ( $wpfCheckboxItemsLength > 5 ) {
				$( this ).addClass( 'hide-items' );
				$( this ).append(
					`<li><button class="list-control-btn" type="button">See all &nbsp;&nbsp;+ ${
						$wpfCheckboxItemsLength - 5
					}</button></li>`
				);
			}
		} );

		$( '.list-control-btn' ).on( 'click', function () {
			$( this )
				.closest( '.wpfFilterVerScroll' )
				.toggleClass( 'hide-items' );
		} );
	}

	$wpfClearButton.on( 'click', () => {
		$wpfCheckboxItems.find( '.wpfDisplay' ).removeClass( 'active' );
	} );
} );
