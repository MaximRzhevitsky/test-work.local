import liveDom from './live-dom';

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

function addStyle(href) {
	const linkElement = document.createElement('link');
	linkElement.setAttribute('rel', 'stylesheet');
	linkElement.setAttribute('type', 'text/css');
	linkElement.setAttribute('href', href);

	document.head.appendChild(linkElement);
}

function addScripts(start = false) {
	if (start && insertScriptsRunning) {
		return;
	}

	insertScriptsRunning = true;

	if (window.gform && window.gform.domLoaded !== true) {
		window.gform.domLoaded = true;
	}

	if (scripts.length) {
		const script = scripts.shift();
		if (!document.querySelector(`#${script.id}`)) {
			const scriptElement = document.createElement('script');
			scriptElement.id = script.id;
			scriptElement.async = false;
			if (script.src) {
				scriptElement.src = script.src;
			}
			if (script.type) {
				scriptElement.type = script.type;
			}
			document.body.appendChild(scriptElement);
		}

		addScripts();
	} else {
		insertScriptsRunning = false;
	}
}

function reloadForm() {
	submitted.placeholder.style.paddingTop = submitted.paddingTop;

	fetch(window.location, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify({
			optimisation_gf_form_id: submitted.id,
			title: submitted.title,
			description: submitted.description,
		}),
	})
		.then((response) => response.json())
		.then((json) => {
			scripts.push(...json.data.scripts);
			let conformationText = '';
			const conformation = submitted.placeholder.querySelector(
				'.gform_confirmation_wrapper'
			);
			if (conformation) {
				conformationText = conformation.outerHTML;
			}

			// eslint-disable-next-line no-use-before-define
			viewForm(
				submitted.id,
				submitted.placeholder,
				json.data.html + conformationText
			);
			submitted.placeholder.style.paddingTop = '';
		});
}

function setReloadEvent() {
	if (reloadEventIsSet) {
		return;
	}

	if (typeof jQuery !== 'undefined') {
		reloadEventIsSet = true;
		// We can't catch jQuery custom event
		jQuery(document).on('gform_confirmation_loaded', () => {
			reloadForm();
		});
	}
}

/**
 * @param {string}         id
 * @param {HTMLDivElement} placeholder
 * @param {string}         html
 */
function viewForm(id, placeholder, html) {
	placeholder.innerHTML = html;
	Array.from(placeholder.querySelectorAll('script')).forEach((oldScript) => {
		const newScript = document.createElement('script');
		Array.from(oldScript.attributes).forEach((attr) =>
			newScript.setAttribute(attr.name, attr.value)
		);
		newScript.appendChild(document.createTextNode(oldScript.innerHTML));
		oldScript.parentNode.replaceChild(newScript, oldScript);
	});
	addScripts(true);

	placeholder.querySelector('form').addEventListener('submit', () => {
		submitted.id = id;
		submitted.placeholder = placeholder;
		submitted.title = placeholder.querySelector('.gform_title') ? '1' : '0';
		submitted.description = placeholder.querySelector('.gform_description')
			? '1'
			: '0';
		submitted.paddingTop = `${placeholder.offsetHeight}px`;

		const oldConfirmation = submitted.placeholder.querySelector(
			'.gform_confirmation_wrapper'
		);
		if (oldConfirmation) {
			oldConfirmation.remove();
		}

		setReloadEvent();
	});
}

liveDom('[data-optimisation-gf-form-id]')
	.firstShow(function initForm() {
		if (!insertStyles) {
			addStyle(
				'/wp-content/themes/child-theme/assets/css/gravity-forms.css'
			);
			insertStyles = true;
		}

		const { optimisationGfFormId } = this.dataset;
		fetch(window.location, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({
				optimisation_gf_form_id: optimisationGfFormId,
				title: this.dataset.title,
				description: this.dataset.description,
			}),
		})
			.then((response) => response.json())
			.then((json) => {
				this.classList.remove('animation');
				this.classList.add('loaded');
				scripts.push(...json.data.scripts);
				viewForm(optimisationGfFormId, this, json.data.html);
			});
	})
	.show(function startAnimation() {
		if (!this.classList.contains('loaded')) {
			this.classList.add('animation');
		}
	})
	.hide(function stopAnimation() {
		if (!this.classList.contains('loaded')) {
			this.classList.remove('animation');
		}
	})
	.setMargin(400, 400);
