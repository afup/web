'use strict';

let overlayElement = null;

let createElement = function (textContent)
{
	overlayElement = document.createElement('div');
	overlayElement.setAttribute('id', 'overlayOnIframe');

	let text = document.createTextNode(textContent);
	overlayElement.appendChild(text);

	document.body.appendChild(overlayElement);

	return overlayElement;
};

let IframeOverlay = {};
IframeOverlay = function(iframe, clickCallback, text) {
	this.iframe = iframe;
	this.clickCallback = clickCallback;
	this.text = text;
};

IframeOverlay.prototype = {
	iframe: null,
	clickCallback: null,
	overlayColor: 'rgba(120, 120, 120, .8)',
	text: null,

	/**
	 * Creates an overlay in the parent window.
	 * Values x and y are referring to the position of the overlay IN the iframe
	 *
	 * @param {String} reference is given to the callback as the first parameter
	 * @param {Int} x
	 * @param {Int} y
	 * @param {Int} width
	 * @param {Int} height
	 */
	show: function(reference, x, y, width, height) {
		this.remove();

		let iframeParams = this.iframe.getBoundingClientRect();
		createElement(this.text);

		x += iframeParams.left;
		y += iframeParams.top;

		overlayElement.setAttribute(
			'style',
			'position: fixed; display: block; z-index: 1000; ' +
			'top: ' + y + 'px; left: ' + x + 'px;' +
			'cursor: pointer;' +
			'width: ' + width + 'px; height: ' + height + 'px; ' +
			'background-color: ' + this.overlayColor + ';' +
			'font-size: 20px; color:white; text-align:center;'
		);

		overlayElement.addEventListener('click', event => { this.clickCallback(reference, event); });
		overlayElement.addEventListener('mouseleave', this.remove);
	},
	remove: function() {
		if (overlayElement !== null) {
			overlayElement.remove();
			overlayElement = null;
		}
	}
};
export { IframeOverlay };
