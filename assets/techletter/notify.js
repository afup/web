'use strict';

const DURATION = 2000;

let container = null;

function show(message, type, role) {
	if (container === null) {
		container = document.createElement('div');
		container.style.cssText = 'position: fixed; right: 1em; bottom: 1em; z-index: 1000; width: 25em;';
		document.body.appendChild(container);
	}

	const element = document.createElement('div');
	element.className = `ui ${type} message`;
	element.setAttribute('role', role);
	element.textContent = message;
	container.appendChild(element);

	setTimeout(() => element.remove(), DURATION);
}

export const notifySuccess = (message) => show(message, 'positive', 'status');
export const notifyError = (message) => show(message, 'negative', 'alert');
