jQuery( ( $ ) => {
	const $select = $( '.gfield select, .woocommerce .form-row select' );
	const $shopFilter = $(
		'.woocommerce-ordering select, .filters-manage select'
	);

	if ( $select.length ) {
		import( /* webpackChunkName: 'select2' */ './libs/select2' ).then(
			() => {
				$select.select2( {
					dropdownCssClass: 'gfield-select-dropdown',
				} );
			}
		);
	}

	if ( $shopFilter.length ) {
		import( /* webpackChunkName: 'select2' */ './libs/select2' ).then(
			() => {
				$shopFilter.select2( {
					dropdownCssClass: 'shop-filter-dropdown',
				} );
			}
		);
	}
} );
