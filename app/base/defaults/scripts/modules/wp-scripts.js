export function enqueueScript(scriptData, dependencies) {
	enqueueDependencies(scriptData.dependencies, dependencies);

	if (scriptData.extra) {
		const extraScript = document.createElement('script');
		extraScript.textContent = scriptData.extra;
		extraScript.id = scriptData.handle + '-js-extra';
		document.head.appendChild(extraScript);
	}

	if (scriptData.before) {
		const beforeScript = document.createElement('script');
		beforeScript.textContent = scriptData.before;
		beforeScript.id = scriptData.handle + '-js-before';
		document.head.appendChild(beforeScript);
	}

	if (scriptData.src) {
		const script = document.createElement('script');

		script.src = scriptData.src;
		script.id = scriptData.handle + '-js';
		if (scriptData.after) {
			script.onload = function () {
				const afterScript = document.createElement('script');
				afterScript.textContent = scriptData.after;
				afterScript.id = scriptData.handle + '-js-after';
				document.head.appendChild(afterScript);
			};
		}

		document.head.appendChild(script);
	} else {
		const afterScript = document.createElement('script');
		afterScript.textContent = scriptData.after;
		afterScript.id = scriptData.handle + '-js-after';
		document.head.appendChild(afterScript);
	}
}

export function isEnqueue(handle) {
	return document.querySelector('#' + handle + '-js');
}

export function enqueueDependencies(dependencyHandles, dependencies) {
	for (const dependencyHandle of dependencyHandles) {
		if (!isEnqueue(dependencyHandle)) {
			enqueueScript(dependencies[dependencyHandle]);
		}
	}
}
