import liveDom from './libs/live-dom';

const scripts = [];
const submitted = {
	id: '0',
	title: '0',
	description: '0',
	placeholder: null,
	paddingTop: '',
};

let insertScriptsRunning = false;
let insertStyles = false;
let reloadEventIsSet = false;
const insertedScripts = [];

function addStyle( href ) {
	const linkElement = document.createElement( 'link' );
	linkElement.setAttribute( 'rel', 'stylesheet' );
	linkElement.setAttribute( 'type', 'text/css' );
	linkElement.setAttribute( 'href', href );

	document.head.appendChild( linkElement );
}

function addScript( script, setId = true ) {
	const scriptElement = document.createElement( 'script' );

	scriptElement.async = false;
	if ( setId ) {
		scriptElement.id = script.id;
	}
	if ( script.src ) {
		scriptElement.src = script.src;
	}
	if ( script.type ) {
		scriptElement.type = script.type;
	}
	document.body.appendChild( scriptElement );
}

function addScripts( start = false ) {
	if ( start && insertScriptsRunning ) {
		return;
	}

	insertScriptsRunning = true;

	if ( window.gform && window.gform.domLoaded !== true ) {
		window.gform.domLoaded = true;
	}

	if ( scripts.length ) {
		const script = scripts.shift();

		if ( script.id.startsWith( 'gform-init-' ) ) {
			addScript( script, false );
		} else if (
			! document.querySelector( `#${ script.id }` ) &&
			! insertedScripts.includes( script.src )
		) {
			addScript( script );
			insertedScripts.push( script.src );
		}

		addScripts();
	} else {
		insertScriptsRunning = false;
	}
}

function fetchForm( formId, title, description ) {
	return fetch(
		'/wp-content/mu-plugins/wld-plugins/wld-gf-load-optimization/output.php',
		{
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify( {
				optimisation_gf_form_id: formId,
				title,
				description,
				page_url: window.location.toString(),
			} ),
		}
	).then( ( response ) => response.json() );
}

function reloadForm() {
	submitted.placeholder.style.paddingTop = submitted.paddingTop;

	fetchForm( submitted.id, submitted.title, submitted.description ).then(
		( json ) => {
			scripts.push( ...json.data.scripts );
			let conformationText = '';
			const conformation = submitted.placeholder.querySelector(
				'.gform_confirmation_wrapper'
			);
			if ( conformation ) {
				conformationText = conformation.outerHTML;
			}

			// eslint-disable-next-line no-use-before-define
			viewForm(
				submitted.id,
				submitted.placeholder,
				json.data.html + conformationText
			);
			submitted.placeholder.style.paddingTop = '';
		}
	);
}

function setReloadEvent() {
	if ( reloadEventIsSet ) {
		return;
	}

	if ( typeof jQuery !== 'undefined' ) {
		reloadEventIsSet = true;
		// We can't catch jQuery custom event
		jQuery( document ).on( 'gform_confirmation_loaded', () => {
			reloadForm();
		} );
	}
}

/**
 * @param {string}         id
 * @param {HTMLDivElement} placeholder
 * @param {string}         html
 */
function viewForm( id, placeholder, html ) {
	placeholder.innerHTML = html;
	Array.from( placeholder.querySelectorAll( 'script' ) ).forEach(
		( oldScript ) => {
			const newScript = document.createElement( 'script' );
			Array.from( oldScript.attributes ).forEach( ( attr ) =>
				newScript.setAttribute( attr.name, attr.value )
			);
			newScript.appendChild(
				document.createTextNode( oldScript.innerHTML )
			);
			oldScript.parentNode.replaceChild( newScript, oldScript );
		}
	);
	addScripts( true );

	placeholder.querySelector( 'form' ).addEventListener( 'submit', () => {
		submitted.id = id;
		submitted.placeholder = placeholder;
		submitted.title = placeholder.querySelector( '.gform_title' )
			? '1'
			: '0';
		submitted.description = placeholder.querySelector(
			'.gform_description'
		)
			? '1'
			: '0';
		submitted.paddingTop = `${ placeholder.offsetHeight }px`;

		const oldConfirmation = submitted.placeholder.querySelector(
			'.gform_confirmation_wrapper'
		);
		if ( oldConfirmation ) {
			oldConfirmation.remove();
		}

		setReloadEvent();
	} );
}

liveDom( '[data-optimisation-gf-form-id]', {
	firstShow: function initForm() {
		if ( ! insertStyles ) {
			addStyle(
				'/wp-content/themes/child-theme/assets/css/gravity-forms.css'
			);
			insertStyles = true;
		}

		const {
			optimisationGfFormId: formId,
			title,
			description,
		} = this.dataset;

		fetchForm( formId, title, description ).then( ( json ) => {
			this.classList.remove( 'animation' );
			this.classList.add( 'loaded' );
			scripts.push( ...json.data.scripts );
			viewForm( formId, this, json.data.html );
		} );
	},
	show: function startAnimation() {
		if ( ! this.classList.contains( 'loaded' ) ) {
			this.classList.add( 'animation' );
		}
	},
	hide: function stopAnimation() {
		if ( ! this.classList.contains( 'loaded' ) ) {
			this.classList.remove( 'animation' );
		}
	},
	setMargin: [ 600, 400 ],
} );
