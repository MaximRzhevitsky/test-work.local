export default function accessibilityTabs( tabListNode ) {
	const tabs = [];
	const tabPanels = [];

	let firstTab = null;
	let lastTab = null;

	function setSelectedTab( currentTab ) {
		const tabsLength = tabs.length;
		for ( let i = 0; i < tabsLength; i += 1 ) {
			const tab = tabs[ i ];
			if ( currentTab === tab ) {
				tab.setAttribute( 'aria-selected', 'true' );
				tab.removeAttribute( 'tabindex' );
				tabPanels[ i ].classList.remove( 'is-hidden' );
			} else {
				tab.setAttribute( 'aria-selected', 'false' );
				tab.tabIndex = -1;
				tabPanels[ i ].classList.add( 'is-hidden' );
			}
		}
	}

	function moveFocusToTab( currentTab ) {
		currentTab.focus();
	}

	function moveFocusToPreviousTab( currentTab ) {
		let index;

		if ( currentTab === firstTab ) {
			moveFocusToTab( lastTab );
		} else {
			index = tabs.indexOf( currentTab );
			moveFocusToTab( tabs[ index - 1 ] );
		}
	}

	function moveFocusToNextTab( currentTab ) {
		let index;

		if ( currentTab === lastTab ) {
			moveFocusToTab( firstTab );
		} else {
			index = tabs.indexOf( currentTab );
			moveFocusToTab( tabs[ index + 1 ] );
		}
	}

	function onKeydown( event ) {
		const tgt = event.currentTarget;
		let flag = false;

		switch ( event.key ) {
			case 'ArrowLeft':
				moveFocusToPreviousTab( tgt );
				flag = true;
				break;
			case 'ArrowRight':
				moveFocusToNextTab( tgt );
				flag = true;
				break;
			case 'Home':
				moveFocusToTab( firstTab );
				flag = true;
				break;
			case 'End':
				moveFocusToTab( lastTab );
				flag = true;
				break;
			default:
				break;
		}

		if ( flag ) {
			event.stopPropagation();
			event.preventDefault();
		}
	}

	function onClick( event ) {
		setSelectedTab( event.currentTarget );
	}

	function init() {
		tabs.push(
			...Array.from( tabListNode.querySelectorAll( '[role=tab]' ) )
		);

		const tabsLength = tabs.length;
		for ( let i = 0; i < tabsLength; i += 1 ) {
			const tab = tabs[ i ];
			const tabPanel = document.getElementById(
				tab.getAttribute( 'aria-controls' )
			);

			tab.tabIndex = -1;
			tab.setAttribute( 'aria-selected', 'false' );
			tabPanels.push( tabPanel );

			tab.addEventListener( 'keydown', onKeydown );
			tab.addEventListener( 'click', onClick );

			if ( ! firstTab ) {
				firstTab = tab;
			}
			lastTab = tab;
		}

		setSelectedTab( firstTab );
	}

	init();
}
