export default function accessibilityMenu( menuNode ) {
	let menuIdIndex = 0;
	const popups = [];
	const menuitemGroups = {};
	const menuOrientation = {};
	const isPopup = {};
	const isPopout = {};
	const firstMenuitem = {};
	const lastMenuitem = {};

	function hasPopup( menuitem ) {
		return menuitem.getAttribute( 'aria-haspopup' ) === 'true';
	}

	function isOpen( menuitem ) {
		return menuitem.getAttribute( 'aria-expanded' ) === 'true';
	}

	function isMenubar( menuId ) {
		return ! isPopup[ menuId ] && ! isPopout[ menuId ];
	}

	function isMenuHorizontal( menuitem ) {
		return menuOrientation[ menuitem ] === 'horizontal';
	}

	function findMenuItems( depth, node, nodes ) {
		let role;
		let flag;

		while ( node ) {
			flag = true;
			role = node.getAttribute( 'data-role' );

			if ( role ) {
				role = role.trim().toLowerCase();
			}

			switch ( role ) {
				case 'menu':
					node.tabIndex = -1;
					init( node, depth + 1 );
					flag = false;
					break;

				case 'menuitem':
					if ( node.getAttribute( 'aria-haspopup' ) === 'true' ) {
						popups.push( node );
					}
					nodes.push( node );
					break;

				default:
					break;
			}

			if (
				flag &&
				node.firstElementChild &&
				node.firstElementChild.tagName !== 'svg'
			) {
				findMenuItems( depth, node.firstElementChild, nodes );
			}
			node = node.nextElementSibling;
		}
	}

	function getMenuItems( menu, depth ) {
		const nodes = [];

		findMenuItems( depth, menu.firstElementChild, nodes );
		return nodes;
	}

	function doesNotContain( popup, menuItem ) {
		if ( menuItem ) {
			return ! popup.nextElementSibling.contains( menuItem );
		}
		return true;
	}

	function closePopupAll( menuItem ) {
		const isMenuItem = typeof menuItem !== 'object' ? false : menuItem;

		for ( let i = 0; i < popups.length; i++ ) {
			const popup = popups[ i ];
			if ( doesNotContain( popup, isMenuItem ) && isOpen( popup ) ) {
				const cmi = popup.nextElementSibling;
				if ( cmi ) {
					popup.setAttribute( 'aria-expanded', 'false' );
					cmi.classList.remove( 'open' );
				}
			}
		}
	}

	function setFocusToMenuitem( menuId, newMenuitem ) {
		closePopupAll( newMenuitem );

		if ( menuitemGroups[ menuId ] ) {
			menuitemGroups[ menuId ].forEach( ( item ) => {
				if ( item === newMenuitem ) {
					item.tabIndex = 0;
					newMenuitem.focus();
				} else {
					item.tabIndex = -1;
				}
			} );
		}
	}

	function setFocusToFirstMenuitem( menuId ) {
		setFocusToMenuitem( menuId, firstMenuitem[ menuId ] );
	}

	function setFocusToLastMenuitem( menuId ) {
		setFocusToMenuitem( menuId, lastMenuitem[ menuId ] );
	}

	function setFocusToPreviousMenuitem( menuId, currentMenuitem ) {
		let newMenuitem;
		let index;

		if ( currentMenuitem === firstMenuitem[ menuId ] ) {
			newMenuitem = lastMenuitem[ menuId ];
		} else {
			index = menuitemGroups[ menuId ].indexOf( currentMenuitem );
			newMenuitem = menuitemGroups[ menuId ][ index - 1 ];
		}

		setFocusToMenuitem( menuId, newMenuitem );

		return newMenuitem;
	}

	function setFocusToNextMenuitem( menuId, currentMenuitem ) {
		let newMenuitem;
		let index;

		if ( currentMenuitem === lastMenuitem[ menuId ] ) {
			newMenuitem = firstMenuitem[ menuId ];
		} else {
			index = menuitemGroups[ menuId ].indexOf( currentMenuitem );
			newMenuitem = menuitemGroups[ menuId ][ index + 1 ];
		}
		setFocusToMenuitem( menuId, newMenuitem );

		return newMenuitem;
	}

	function getIdFromAriaLabel( node ) {
		let id = node.dataset.menuId;
		if ( ! id ) {
			node.dataset.menuId = ( ++menuIdIndex ).toString();
			id = node.dataset.menuId;
		}
		return id;
	}

	function getMenuOrientation( node ) {
		let orientation = node.getAttribute( 'aria-orientation' );

		if ( ! orientation ) {
			const role = node.getAttribute( 'data-role' );

			switch ( role ) {
				case 'menubar':
					orientation = 'horizontal';
					break;

				case 'menu':
					orientation = 'vertical';
					break;

				default:
					break;
			}
		}

		return orientation;
	}

	function getMenuId( node ) {
		let id = false;
		let role = node.getAttribute( 'data-role' );

		while ( node && role !== 'menu' && role !== 'menubar' ) {
			node = node.parentNode;
			if ( node ) {
				role = node.getAttribute( 'data-role' );
			}
		}

		if ( node ) {
			id = `${ role }-${ getIdFromAriaLabel( node ) }`;
		}

		return id;
	}

	function getMenu( menuitem ) {
		let menu = menuitem;
		let role = menuitem.getAttribute( 'data-role' );

		while ( menu && role !== 'menu' && role !== 'menubar' ) {
			menu = menu.parentNode;
			if ( menu ) {
				role = menu.getAttribute( 'data-role' );
			}
		}

		return menu;
	}

	function isAnyPopupOpen() {
		for ( let i = 0; i < popups.length; i++ ) {
			if ( popups[ i ].getAttribute( 'aria-expanded' ) === 'true' ) {
				return true;
			}
		}
		return false;
	}

	function setMenubarDataExpanded( value ) {
		menuNode.setAttribute( 'data-menubar-item-expanded', value );
	}

	function isMenubarDataExpandedTrue() {
		return menuNode.getAttribute( 'data-menubar-item-expanded' ) === 'true';
	}

	function openPopup( menuId, menuitem ) {
		const popupMenu = menuitem.nextElementSibling;

		if ( popupMenu ) {
			const rect = menuitem.getBoundingClientRect();
			if ( isPopup[ menuId ] ) {
				popupMenu.style.left = `${ rect.width - 1 }px`;
				popupMenu.classList.add( 'open' );
			} else {
				popupMenu.classList.add( 'open' );
			}

			menuitem.setAttribute( 'aria-expanded', 'true' );
			setMenubarDataExpanded( 'true' );
			return getMenuId( popupMenu );
		}

		return false;
	}

	function closePopout( menuitem ) {
		let menu;
		let menuId = getMenuId( menuitem );
		let cmi = menuitem;

		while ( isPopup[ menuId ] || isPopout[ menuId ] ) {
			menu = getMenu( cmi );
			cmi = menu.previousElementSibling;
			menuId = getMenuId( cmi );
			menu.classList.remove( 'open' );
		}
		cmi.focus();
		return cmi;
	}

	function closePopup( menuitem ) {
		let menu;
		let cmi = menuitem;
		const menuId = getMenuId( menuitem );

		if ( isMenubar( menuId ) ) {
			if ( isOpen( menuitem ) ) {
				menuitem.setAttribute( 'aria-expanded', 'false' );
				menuitem.nextElementSibling.classList.remove( 'open' );
			}
		} else {
			menu = getMenu( menuitem );
			cmi = menu.previousElementSibling;
			cmi.setAttribute( 'aria-expanded', 'false' );
			cmi.focus();
			menu.classList.remove( 'open' );
		}

		return cmi;
	}

	function onKeydown( event ) {
		const { currentTarget: tgt, key } = event;
		const menuId = getMenuId( tgt );

		let flag = false;
		let id;
		let popupMenuId;
		let mi;

		document.querySelectorAll( '.menu-tooltip' ).forEach( ( tip ) => {
			tip.remove();
		} );

		switch ( key ) {
			case ' ':
				if ( hasPopup( tgt ) ) {
					popupMenuId = openPopup( menuId, tgt );
					setFocusToFirstMenuitem( popupMenuId );
				} else if ( tgt.href !== '#' ) {
					closePopupAll();
					setMenubarDataExpanded( 'false' );
				}
				flag = true;
				break;

			case 'Esc':
			case 'Escape':
				mi = closePopup( tgt );
				id = getMenuId( mi );
				setMenubarDataExpanded( 'false' );
				flag = true;
				break;

			case 'Up':
			case 'ArrowUp':
				if ( isMenuHorizontal( menuId ) ) {
					if ( hasPopup( tgt ) ) {
						popupMenuId = openPopup( menuId, tgt );
						setFocusToLastMenuitem( popupMenuId );
					}
				} else {
					setFocusToPreviousMenuitem( menuId, tgt );
				}
				flag = true;
				break;

			case 'ArrowDown':
			case 'Down':
				if ( isMenuHorizontal( menuId ) ) {
					if ( hasPopup( tgt ) ) {
						popupMenuId = openPopup( menuId, tgt );
						setFocusToFirstMenuitem( popupMenuId );
					}
				} else {
					setFocusToNextMenuitem( menuId, tgt );
				}
				flag = true;
				break;

			case 'Left':
			case 'ArrowLeft':
				if ( isMenuHorizontal( menuId ) ) {
					mi = setFocusToPreviousMenuitem( menuId, tgt );
					if ( isAnyPopupOpen() || isMenubarDataExpandedTrue() ) {
						openPopup( menuId, mi );
					}
				} else if ( isPopout[ menuId ] ) {
					mi = closePopup( tgt );
					id = getMenuId( mi );
					setFocusToMenuitem( id, mi );
				} else {
					mi = closePopup( tgt );
					id = getMenuId( mi );
					mi = setFocusToPreviousMenuitem( id, mi );
					openPopup( id, mi );
				}
				flag = true;
				break;

			case 'Right':
			case 'ArrowRight':
				if ( isMenuHorizontal( menuId ) ) {
					mi = setFocusToNextMenuitem( menuId, tgt );
					if ( isAnyPopupOpen() || isMenubarDataExpandedTrue() ) {
						openPopup( menuId, mi );
					}
				} else if ( hasPopup( tgt ) ) {
					popupMenuId = openPopup( menuId, tgt );
					setFocusToFirstMenuitem( popupMenuId );
				} else {
					mi = closePopout( tgt );
					id = getMenuId( mi );
					mi = setFocusToNextMenuitem( id, mi );
					openPopup( id, mi );
				}
				flag = true;
				break;

			case 'Home':
			case 'PageUp':
				setFocusToFirstMenuitem( menuId, tgt );
				flag = true;
				break;

			case 'End':
			case 'PageDown':
				setFocusToLastMenuitem( menuId, tgt );
				flag = true;
				break;

			case 'Tab':
				setMenubarDataExpanded( 'false' );
				closePopup( tgt );
				break;

			default:
				break;
		}

		if ( flag ) {
			event.stopPropagation();
			event.preventDefault();
		}
	}

	function onMenuitemPointerover( event ) {
		const tgt = event.currentTarget;
		const menuId = getMenuId( tgt );

		closePopupAll( tgt );
		if ( hasPopup( tgt ) ) {
			openPopup( menuId, tgt );
		}
	}

	function onBackgroundPointerdown( event ) {
		if (
			event.relatedTarget &&
			! menuNode.contains( event.relatedTarget )
		) {
			closePopupAll();
		}
	}

	function setTooltip( event ) {
		const { currentTarget: tgt, key } = event;
		const tooltip = !! popups.length
			? 'use &#8592; &#8595; &#8594; to navigate'
			: 'use &#8592; / &#8594; to navigate';
		if (
			key === 'Tab' ||
			key === 'Left' ||
			key === 'ArrowLeft' ||
			key === 'Right' ||
			key === 'ArrowRight'
		) {
			tgt.innerHTML += `<span class="menu-tooltip" aria-hidden="true">${ tooltip }</span>`;
		}
	}

	function init( menu, depth ) {
		const menuId = getMenuId( menu );
		const menuItems = getMenuItems( menu, depth );

		menuOrientation[ menuId ] = getMenuOrientation( menu );

		isPopup[ menuId ] =
			menu.getAttribute( 'data-role' ) === 'menu' && depth === 1;
		isPopout[ menuId ] =
			menu.getAttribute( 'data-role' ) === 'menu' && depth > 1;

		menuitemGroups[ menuId ] = [];
		firstMenuitem[ menuId ] = null;
		lastMenuitem[ menuId ] = null;

		for ( let i = 0; i < menuItems.length; i++ ) {
			const menuitem = menuItems[ i ];
			const role = menuitem.getAttribute( 'data-role' );

			if ( role.indexOf( 'menuitem' ) < 0 ) {
				continue;
			}

			menuitem.tabIndex = -1;
			menuitemGroups[ menuId ].push( menuitem );

			menuitem.addEventListener( 'keydown', onKeydown );
			menuitem.addEventListener( 'pointerover', onMenuitemPointerover );

			if ( ! firstMenuitem[ menuId ] ) {
				if ( hasPopup( menuitem ) ) {
					menuitem.tabIndex = 0;
				}
				firstMenuitem[ menuId ] = menuitem;
			}
			lastMenuitem[ menuId ] = menuitem;
		}
	}

	init( menuNode, 0 );

	menuNode.addEventListener( 'mouseout', onBackgroundPointerdown, true );

	const firstItem = menuNode.querySelector( '[data-role=menuitem]' );
	if ( firstItem ) {
		firstItem.tabIndex = 0;
		firstItem.addEventListener( 'keyup', setTooltip );
	}
}
