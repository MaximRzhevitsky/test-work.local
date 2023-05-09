export default function navigationMenu( menuNode ) {
	let menuIdIndex = 0;
	const menuitemGroups = {};
	const menuOrientation = {};
	const firstMenuitem = {};
	const lastMenuitem = {};
	const subMenuList = [];

	function hasSubItems( menuitem ) {
		return (
			menuitem.nextElementSibling.getAttribute( 'data-role' ) === 'menu'
		);
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
					nodes.push( node );
					if ( node.nextElementSibling && hasSubItems( node ) ) {
						subMenuList.push( node );
					}
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

	function setFocusToMenuitem( menuId, newMenuitem ) {
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

	function onKeydown( event ) {
		const { currentTarget: tgt, key } = event;
		const menuId = getMenuId( tgt );

		let flag = false;
		let id;
		let subItemId;
		let mi;

		document
			.querySelectorAll( '.footer-menu-tooltip' )
			.forEach( ( tip ) => {
				tip.remove();
			} );

		switch ( key ) {
			case 'Esc':
			case 'Escape':
				const menu = getMenu( tgt );
				if ( menu.previousElementSibling ) {
					menu.previousElementSibling.focus();
				}
				flag = true;
				break;

			case 'Up':
			case 'ArrowUp':
				if ( isMenuHorizontal( menuId ) ) {
					if ( tgt.nextElementSibling && hasSubItems( tgt ) ) {
						subItemId = getMenuId( tgt.nextElementSibling );
						setFocusToLastMenuitem( subItemId );
					}
				} else {
					setFocusToPreviousMenuitem( menuId, tgt );
				}
				flag = true;
				break;

			case 'ArrowDown':
			case 'Down':
				if ( isMenuHorizontal( menuId ) ) {
					if ( tgt.nextElementSibling && hasSubItems( tgt ) ) {
						subItemId = getMenuId( tgt.nextElementSibling );
						setFocusToFirstMenuitem( subItemId );
					}
				} else {
					setFocusToNextMenuitem( menuId, tgt );
				}
				flag = true;
				break;

			case 'Left':
			case 'ArrowLeft':
				if ( isMenuHorizontal( menuId ) ) {
					setFocusToPreviousMenuitem( menuId, tgt );
				} else if (
					getMenu( tgt ) &&
					getMenu(
						getMenu( tgt ).previousElementSibling
					).getAttribute( 'data-role' ) !== 'menubar'
				) {
					mi = getMenu( tgt ).previousElementSibling;
					id = getMenuId( mi );
					setFocusToMenuitem( id, mi );
				} else {
					mi = getMenu( tgt ).previousElementSibling;
					id = getMenuId( mi );
					setFocusToPreviousMenuitem( id, mi );
				}
				flag = true;
				break;

			case 'Right':
			case 'ArrowRight':
				if ( isMenuHorizontal( menuId ) ) {
					setFocusToNextMenuitem( menuId, tgt );
				} else if ( tgt.nextElementSibling ) {
					subItemId = getMenuId( tgt.nextElementSibling );
					setFocusToFirstMenuitem( subItemId );
				} else {
					let subMenuItem = tgt;
					let subMenu = getMenu( tgt );
					let role = subMenu.getAttribute( 'data-role' );

					while ( subMenu && role !== 'menubar' ) {
						subMenuItem = subMenu.previousElementSibling;
						subMenu = getMenu( subMenuItem );
						if ( subMenu ) {
							role = subMenu.getAttribute( 'data-role' );
						}
					}

					setFocusToNextMenuitem( getMenuId( subMenu ), subMenuItem );
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

			default:
				break;
		}

		if ( flag ) {
			event.stopPropagation();
			event.preventDefault();
		}
	}

	function setTooltip( event ) {
		const { currentTarget: tgt, key } = event;
		const tooltip = !! subMenuList.length
			? 'use &#8592; &#8595; &#8594; to navigate'
			: 'use &#8592; / &#8594; to navigate';
		if (
			key === 'Tab' ||
			key === 'Left' ||
			key === 'ArrowLeft' ||
			key === 'Right' ||
			key === 'ArrowRight'
		) {
			tgt.innerHTML += `<span class="footer-menu-tooltip" aria-hidden="true">${ tooltip }</span>`;
		}
	}

	function init( menu, depth ) {
		let menuitem;
		let role;

		const menuId = getMenuId( menu );
		const menuItems = getMenuItems( menu, depth );

		menuOrientation[ menuId ] = getMenuOrientation( menu );

		menuitemGroups[ menuId ] = [];
		firstMenuitem[ menuId ] = null;
		lastMenuitem[ menuId ] = null;

		for ( let i = 0; i < menuItems.length; i++ ) {
			menuitem = menuItems[ i ];
			role = menuitem.getAttribute( 'data-role' );

			if ( role.indexOf( 'menuitem' ) < 0 ) {
				continue;
			}

			menuitem.tabIndex = -1;
			menuitemGroups[ menuId ].push( menuitem );

			menuitem.addEventListener( 'keydown', onKeydown );

			if ( ! firstMenuitem[ menuId ] ) {
				firstMenuitem[ menuId ] = menuitem;
			}
			lastMenuitem[ menuId ] = menuitem;
		}
	}

	init( menuNode, 0 );

	const firstItem = menuNode.querySelector( '[data-role=menuitem]' );
	if ( firstItem ) {
		firstItem.tabIndex = 0;
		firstItem.addEventListener( 'keyup', setTooltip );
	}
}
