/**
 * Callback for element add/show/hide events
 *
 * @callback eventCallback
 * @this HTMLElement
 */

// noinspection JSUnusedGlobalSymbols
/**
 * todo
 *
 * @callback dependencyFunction
 * @param {Function} done
 * @param {Function} [error]
 */
class LiveDom {
	constructor( selector ) {
		this._selector = selector;
		if ( ! this._isValidSelector( this._selector ) ) {
			// eslint-disable-next-line no-console
			console.error( 'Invalid Selector', selector );
		}

		this._doneOnceInit = false;
		this._doneOnceAlways = false;
		this._doneOnceShow = false;
		this._doneOnceHide = false;
		this._doneFirstAlways = [];
		this._doneFirstShow = [];
		this._doneFirstHide = [];
		this._observers = {};

		this._margin = { top: 250, bottom: 250 };
		this._observer = null;
		this._elements = [];
		this._stoped = false;
		this._dependencyFunction = null;
		this._dependencyStatus = 'ready';
		this._dependencyMissedCallbacks = [];
		this._dependencyMissedAddElements = [];
		this._autoStop = {
			onceInit: null,
			onceShow: null,
			onceAlways: null,
			onceHide: null,
		};
		this._autoUnobserve = {
			firstInit: null,
			firstShow: null,
			firstAlways: null,
			firstHide: null,
		};
		this._callbacks = {
			onceInit: null,
			onceShow: null,
			onceAlways: null,
			onceHide: null,
			init: null,
			firstShow: null,
			show: null,
			firstAlways: null,
			always: null,
			firstHide: null,
			hide: null,
		};
		this.__callbacks = {
			init: null,
			firstShow: null,
			show: null,
			firstAlways: null,
			always: null,
			firstHide: null,
			hide: null,
		};

		return this;
	}

	/**
	 * Sets the callback for the element init event.
	 * This event fires once when the first element is detected, after which all other observers are deleted.
	 * When setting this event, all other events do not make sense and will not be called.
	 *
	 * @param {eventCallback} callback
	 * @param {boolean}       [autoStop=true]
	 * @return {LiveDom} - LiveDom instance
	 */
	onceInit( callback, autoStop = true ) {
		this._callbacks.onceInit = callback;
		this._autoStop.onceInit = autoStop;
		this.start();
		return this;
	}

	/**
	 * Sets the callback for the element if show or hide events.
	 * This event fires once when the first element is detected, after which all other observers are deleted.
	 * When setting this event, all other events do not make sense and will not be called.
	 *
	 * @param {eventCallback} callback
	 * @param {boolean}       [autoStop=true]
	 * @return {LiveDom} - LiveDom instance
	 */
	onceShow( callback, autoStop = true ) {
		this._callbacks.onceShow = callback;
		this._autoStop.onceShow = autoStop;
		this.start();
		return this;
	}

	/**
	 * Sets the callback for the element if show or hide events.
	 * This event fires once when the first element is detected, after which all other observers are deleted.
	 * When setting this event, all other events do not make sense and will not be called.
	 *
	 * @param {eventCallback} callback
	 * @param {boolean}       [autoStop=true]
	 * @return {LiveDom} - LiveDom instance
	 */
	onceAlways( callback, autoStop = true ) {
		this._callbacks.onceAlways = callback;
		this._autoStop.onceAlways = autoStop;
		this.start();
		return this;
	}

	/**
	 * Sets the callback for the element if show or hide events.
	 * This event fires once when the first element is detected, after which all other observers are deleted.
	 * When setting this event, all other events do not make sense and will not be called.
	 *
	 * @param {eventCallback} callback
	 * @param {boolean}       [autoStop=true]
	 * @return {LiveDom} - LiveDom instance
	 */
	onceHide( callback, autoStop = true ) {
		this._callbacks.onceHide = callback;
		this._autoStop.onceHide = autoStop;
		this.start();
		return this;
	}

	/**
	 * Sets the callback for the element initialized event. This event fires once time an element initialized.
	 *
	 * @param {eventCallback} callback
	 * @param {boolean}       [autoUnobserve=false]
	 * @param {boolean}       [autoStart=true]
	 * @return {LiveDom} - LiveDom instance
	 */
	init( callback, autoUnobserve = false, autoStart = true ) {
		this._callbacks.init = callback;
		this._autoUnobserve.firstInit = autoUnobserve;
		if ( autoStart === true ) {
			this.start();
		}
		return this;
	}

	/**
	 * Sets the callback for the element show event. This event fires once time an element shown.
	 *
	 * @param {eventCallback} callback
	 * @param {boolean}       [autoUnobserve=false]
	 * @param {boolean}       [autoStart=true]
	 * @return {LiveDom} - LiveDom instance
	 */
	firstShow( callback, autoUnobserve = false, autoStart = true ) {
		this._callbacks.firstShow = callback;
		this._autoUnobserve.firstShow = autoUnobserve;
		if ( autoStart === true ) {
			this.start();
		}
		return this;
	}

	/**
	 * Sets the callback for the element show event. This event fires every time an element shown.
	 *
	 * @param {eventCallback} callback
	 * @return {LiveDom} - LiveDom instance
	 */
	show( callback ) {
		this._callbacks.show = callback;
		return this;
	}

	/**
	 * Sets the callback for the element hide event. This event fires once time an element hiding.
	 *
	 * @param {eventCallback} callback
	 * @param {boolean}       [autoUnobserve=false]
	 * @param {boolean}       [autoStart=true]
	 * @return {LiveDom} - LiveDom instance
	 */
	firstHide( callback, autoUnobserve = false, autoStart = true ) {
		this._callbacks.firstHide = callback;
		this._autoUnobserve.firstHide = autoUnobserve;
		if ( autoStart === false ) {
			this.start();
		}
		return this;
	}

	/**
	 * Sets the callback for the element hide event. This event fires every time an element hiding.
	 *
	 * @param {eventCallback} callback
	 * @return {LiveDom} - LiveDom instance
	 */
	hide( callback ) {
		this._callbacks.hide = callback;
		return this;
	}

	/**
	 * Sets the callback for the element appearance event. This event fires once time an element shown or hiding.
	 *
	 * @param {eventCallback} callback
	 * @param {boolean}       [autoUnobserve=false]
	 * @param {boolean}       [autoStart=true]
	 * @return {LiveDom} - LiveDom instance
	 */
	firstAlways( callback, autoUnobserve = false, autoStart = true ) {
		this._callbacks.firstAlways = callback;
		this._autoUnobserve.firstAlways = autoUnobserve;
		if ( autoStart === false ) {
			this.start();
		}
		return this;
	}

	/**
	 * Sets the callback for the element appearance event. This event fires every time an element shown or hiding.
	 *
	 * @param {eventCallback} callback
	 * @return {LiveDom} - LiveDom instance
	 */
	always( callback ) {
		this._callbacks.always = callback;
		return this;
	}

	/**
	 * todo
	 *
	 * @param {dependencyFunction} dependencyFunction
	 * @return {LiveDom} - LiveDom instance
	 */
	dependency( dependencyFunction ) {
		this._dependencyFunction = dependencyFunction;
		this._dependencyStatus = 'not_ready';
		return this;
	}

	/**
	 * todo
	 *
	 * @param {number} [top=250]
	 * @param {number} [bottom=250]
	 * @return {LiveDom} - LiveDom instance
	 */
	setMargin( top = 250, bottom = 250 ) {
		this._margin.top = top;
		this._margin.bottom = bottom;
		return this;
	}

	/**
	 * todo
	 *
	 * @param {HTMLElement} element
	 * @return {LiveDom} - LiveDom instance
	 */
	addElement( element ) {
		if ( this._elements.includes( element ) ) {
			return this;
		}

		this._elements.push( element );

		// noinspection JSUnresolvedVariable
		if ( typeof element.liveBlock === 'undefined' ) {
			element.liveBlock = {};
		}

		// noinspection JSUnresolvedVariable
		if ( typeof element.liveBlock[ this._selector ] === 'undefined' ) {
			// noinspection JSUnresolvedVariable
			element.liveBlock[ this._selector ] = [];
		}

		// noinspection JSUnresolvedVariable
		element.liveBlock[ this._selector ].push( this );

		this._initElement( element );

		if ( this._stoped === false ) {
			if ( typeof IntersectionObserver === 'undefined' ) {
				this._badBrowsers( element );
			} else {
				this._goodBrowsers( element );
			}
		}

		return this;
	}

	/**
	 * @param {HTMLElement} element
	 */
	_badBrowsers( element ) {
		const done = () => {
			this._dependencyStatus = 'ready';
			this._badBrowsers( element );
		};

		const error = ( e ) => {
			this._observers[ element ].unobserve( element );
			if ( e ) {
				// eslint-disable-next-line no-console
				console.error( e );
			}
		};

		if ( this._dependencyStatus === 'ready' ) {
			Object.keys( this._callbacks ).forEach( ( callback ) => {
				if ( this.__callbacks[ callback ] ) {
					this._callbacks[ callback ].bind( element )();
				}
				if ( this._callbacks[ callback ] ) {
					this._callbacks[ callback ].bind( element )();
				}
			} );
		} else if ( this._dependencyStatus === 'not_ready' ) {
			this._dependencyStatus = 'process';
			this._dependencyFunction( done, error );
		}
	}

	/**
	 * @param {HTMLElement} element
	 */
	_goodBrowsers( element ) {
		let intersectionObserverInit = false;

		this._observers[ element ] = new IntersectionObserver(
			( [ entry ] ) => {
				if ( intersectionObserverInit || entry.intersectionRatio > 0 ) {
					intersectionObserverInit = true;
					if ( entry.isIntersecting ) {
						this._runElement( element, 'show' );
					} else if ( entry.isIntersecting === false ) {
						this._runElement( element, 'hide' );
					}
					this._runElement( element, 'always' );
				} else {
					intersectionObserverInit = true;
				}
			},
			{
				rootMargin: `${ this._margin.bottom }px 0px ${ this._margin.top }px 0px`,
				threshold: [ 0.01 ],
			}
		);

		this._observers[ element ].observe( element );
	}

	/**
	 * @param {HTMLElement} element
	 */
	_initElement( element ) {
		if (
			this &&
			( this._callbacks.init ||
				this.__callbacks.init ||
				this._callbacks.onceInit )
		) {
			const done = () => {
				this._dependencyStatus = 'ready';
				this._dependencyMissedAddElements.forEach( this._initElement );
				this._dependencyMissedAddElements = [];
			};
			const error = ( e ) => {
				this._observers[ element ].unobserve( element );
				if ( e ) {
					// eslint-disable-next-line no-console
					console.error( e );
				}
			};

			if ( this._dependencyStatus === 'ready' ) {
				if ( this._callbacks.onceInit ) {
					if ( this._doneOnceInit === false ) {
						this._doneOnceInit = true;
						this._callbacks.onceInit.bind( element )();
						if ( this._autoStop.onceInit ) {
							this.stop();
						}
					}
					return;
				}
				if ( this.__callbacks.init ) {
					this.__callbacks.init.bind( element )();
				}
				if ( this._callbacks.init ) {
					this._callbacks.init.bind( element )();
				}
			} else if ( this._dependencyStatus === 'not_ready' ) {
				this._dependencyStatus = 'process';
				this._dependencyMissedAddElements.push( element );
				this._dependencyFunction( done, error );
			} else if ( this._dependencyStatus === 'process' ) {
				this._dependencyMissedAddElements.push( element );
			}
		}
	}

	/**
	 * @param {HTMLElement} element
	 * @param {string}      type
	 */
	_runElement( element, type ) {
		if ( this._dependencyStatus === 'ready' ) {
			const Type = type.charAt( 0 ).toUpperCase() + type.slice( 1 );

			if (
				this._callbacks[ `once${ Type }` ] &&
				this[ `_once${ Type }` ] === false
			) {
				this[ `_once${ Type }` ] = true;
				this._callbacks[ `once${ Type }` ].bind( element )();
				if ( this._autoStop[ `once${ Type }` ] ) {
					this.stop();
				}
			}

			if ( ! this[ `_doneFirst${ Type }` ].includes( element ) ) {
				this[ `_doneFirst${ Type }` ].push( element );
				if ( this.__callbacks[ `first${ Type }` ] ) {
					this.__callbacks[ `first${ Type }` ].bind( element )();
				}
				if ( this._callbacks[ `first${ Type }` ] ) {
					this._callbacks[ `first${ Type }` ].bind( element )();
					if ( this._autoUnobserve[ `first${ Type }` ] ) {
						this._observers[ element ].unobserve( element );
					}
				}
			}

			if ( this.__callbacks[ type ] ) {
				this.__callbacks[ type ].bind( element )();
			}
			if ( this._callbacks[ type ] ) {
				this._callbacks[ type ].bind( element )();
			}
		} else if ( this._dependencyStatus === 'not_ready' ) {
			const done = () => {
				this._dependencyStatus = 'ready';
				this._dependencyMissedCallbacks.forEach(
					( { element: missedElement, type: missedType } ) => {
						this._runElement( missedElement, missedType );
					}
				);
				this._dependencyMissedCallbacks = [];
			};
			const error = ( e ) => {
				this._observers[ element ].unobserve( element );
				if ( e ) {
					// eslint-disable-next-line no-console
					console.error( e );
				}
			};

			this._dependencyStatus = 'process';
			this._dependencyMissedCallbacks.push( { type, element } );
			this._dependencyFunction( done, error );
		} else if ( this._dependencyStatus === 'process' ) {
			this._dependencyMissedCallbacks.push( { type, element } );
		}
	}

	/**
	 * todo
	 *
	 * @return {LiveDom} - LiveDom instance
	 */
	start() {
		if (
			null === this._observer &&
			this._isValidSelector( this._selector )
		) {
			this._observer = new MutationObserver( ( mutationsList ) => {
				mutationsList.forEach( ( mutation ) => {
					if (
						mutation.type === 'childList' &&
						mutation.addedNodes.length
					) {
						mutation.addedNodes.forEach( ( element ) => {
							if ( element instanceof HTMLElement ) {
								if ( element.matches( this._selector ) ) {
									this.addElement( element );
								}

								element
									.querySelectorAll( this._selector )
									.forEach( ( childElement ) => {
										this.addElement( childElement );
									} );
							}
						} );
					}
				} );
			} );

			const bodyInit = () => {
				if ( document.body ) {
					this._observer.observe( document.body, {
						subtree: true,
						childList: true,
					} );
					document
						.querySelectorAll( this._selector )
						.forEach( ( element ) => this.addElement( element ) );
				} else {
					window.setTimeout( bodyInit, 50 );
				}
			};

			bodyInit();
		}

		return this;
	}

	stop() {
		this._stoped = true;
		this._observer.disconnect();
		this._elements.forEach( ( element ) => {
			if ( typeof this._observers[ element ] !== 'undefined' ) {
				this._observers[ element ].unobserve( element );
			}
		} );
	}

	_isValidSelector( selector ) {
		try {
			document.createDocumentFragment().querySelector( selector );
		} catch ( e ) {
			return false;
		}
		return true;
	}
}

export { LiveDom };

/**
 * @param {string} selector
 * @return {LiveDom} - LiveDom instance
 */
export default function liveDom( selector ) {
	return new LiveDom( selector );
}
