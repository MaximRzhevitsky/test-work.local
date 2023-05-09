import { isEnqueue, enqueueScript } from './scripts.js';

window.addEventListener( 'load', () => {
	const script = document.querySelector( '#theme-delay-scripts-js' );
	const delayScriptsByTimeout = JSON.parse( script.dataset.scriptsByTimeout );
	const delayScriptsByActivity = JSON.parse(
		script.dataset.scriptsByActivity
	);
	const delayScriptsByView = JSON.parse( script.dataset.scriptsByView );
	const dependencies = JSON.parse( script.dataset.dependencies );

	if ( delayScriptsByTimeout ) {
		for ( const timeout in delayScriptsByTimeout ) {
			setTimeout( () => {
				const delayScripts = delayScriptsByTimeout[ timeout ];
				for ( const delayScriptHandle in delayScripts ) {
					const delayScript = delayScripts[ delayScriptHandle ];
					if ( ! isEnqueue( delayScriptHandle ) ) {
						enqueueScript( delayScript, dependencies );
					}
				}
			}, parseInt( timeout ) );
		}
	}

	if ( delayScriptsByActivity ) {
		for ( const timeout in delayScriptsByActivity ) {
			// eslint-disable-next-line no-undef
			userActiveAction( () => {
				const delayScripts = delayScriptsByActivity[ timeout ];
				for ( const delayScriptHandle in delayScripts ) {
					const delayScript = delayScripts[ delayScriptHandle ];
					if ( ! isEnqueue( delayScriptHandle ) ) {
						enqueueScript( delayScript, dependencies );
					}
				}
			}, parseInt( timeout ) );
		}
	}

	if ( delayScriptsByView ) {
		for ( const timeout in delayScriptsByView ) {
			const delayScripts = delayScriptsByView[ timeout ];
			for ( const delayScriptHandleSelector in delayScripts ) {
				const delayScript = delayScripts[ delayScriptHandleSelector ];
				const [ delayScriptHandle, delayScriptSelector ] =
					delayScriptHandleSelector.split( '~|~' );

				// eslint-disable-next-line no-undef
				themeViewAction(
					() => {
						if ( ! isEnqueue( delayScriptHandle ) ) {
							enqueueScript( delayScript, dependencies );
						}
					},
					delayScriptSelector,
					parseInt( timeout )
				);
			}
		}
	}
} );
