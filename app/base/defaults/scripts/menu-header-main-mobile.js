import liveDom from './libs/live-dom';

liveDom( '.menu-header-main-mobile' ).firstShow( function () {
	this.querySelectorAll(
		'.menu-header-main-mobile__item_has-sub-items'
	).forEach( ( itemHasSubItems ) => {
		const expandWrapper = document.createElement( 'div' );
		const expandButton = document.createElement( 'button' );

		expandWrapper.classList.add(
			'menu-header-main-mobile__expand-wrapper'
		);
		expandButton.classList.add( 'menu-header-main-mobile__expand-btn' );
		expandButton.insertAdjacentHTML(
			'afterbegin',
			'<span class="screen-reader-text">Expand</span>'
		);
		expandButton.addEventListener( 'click', ( event ) => {
			const item = event.currentTarget.closest(
				'.menu-header-main-mobile__item'
			);

			item.querySelector(
				'.menu-header-main-mobile__sub-items'
			).classList.toggle( 'menu-header-main-mobile__sub-items_open' );

			item.querySelector(
				'.menu-header-main-mobile__expand-btn'
			).classList.toggle( 'menu-header-main-mobile__expand-btn_open' );
		} );

		itemHasSubItems.prepend( expandWrapper );

		expandWrapper.append(
			itemHasSubItems.querySelector( 'a' ),
			expandButton
		);
	} );
} );
