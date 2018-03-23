import { LinkEditor } from './linkEditor';
import { IframeOverlay } from './iframeOverlay';

(function(w, d) {
	'use strict';
	let iframeOverlay, iframePlusOverlay;

	let form = d.querySelector('#form-update');
	/**
	 * @todo should not be global, we should use events
	 * @type {Notyf}
	 */
	w.notyf = new Notyf();

	let linkEditor = new LinkEditor(document.querySelector('#link-editor'));

	w.updatePreview = function ()
	{
		// @todo add overlay when iframe is loading
		form.querySelector('input[name=techletter],textarea[name=techletter]').value = JSON.stringify(techletter);
		form.submit();
	};

	// Make sure the preview is loaded when loading the page
	updatePreview();

	w.editableElements = [
		{
			type: "news",
			container: '#templateNews',
			selector: 'li',
			maxItems: 2,
			newItemType: 'li',
			model: {
				url: {inputType: "input", type: "url"},
				title: {inputType: "input", type: "text"},
				date: {inputType: "input", type: "date"}
			}
		},
		{
			type: "articles",
			container: '#templateArticles',
			selector: 'div.template--article',
			maxItems: 10,
			newItemType: 'div',
			model: {
				url: {inputType: "input", type: "url"},
				title: {inputType: "input", type: "text"},
				host: {inputType: "input", type: "text"},
				readingTime: {inputType: "input", type: "number", min: 1, max: 100, step: 1},
				excerpt: {inputType: "textarea", rows: 10}
			}
		},
		{
			type: "projects",
			container: '#templateProjects',
			selector: 'li',
			maxItems: 5,
			newItemType: 'div',
			model: {
				url: {inputType: "input", type: "url"},
				name: {inputType: "input", type: "text"},
				description: {inputType: "input", type: "text"}
			}
		}
	];

	let onEditableElement = function(event)
	{
		let elementPosition = event.toElement.getBoundingClientRect();
		iframeOverlay.show(
			event.toElement.dataset.url,
			elementPosition.x,
			elementPosition.y,
			elementPosition.width,
			elementPosition.height
		);
	};

	let onPlusElement = function(event)
	{
		let elementPosition = event.toElement.getBoundingClientRect();
		iframePlusOverlay.show(
			event.toElement.dataset.type,
			elementPosition.x,
			elementPosition.y,
			elementPosition.width,
			elementPosition.height
		);
	};

	let declareListeners = function(iframeDocument)
	{
		editableElements.forEach(item => {
			let items = iframeDocument.querySelectorAll(item.container + ' ' + item.selector);

			if (items.length < item.maxItems) {
				// Add "new item" node
				let plusElement = iframeDocument.createElement(item.newItemType);
				plusElement.setAttribute(
					'style',
					'text-align:center; font-size: 20px; font-weight:bold; color: black; list-style-type:none;'
				);
				plusElement.dataset.type = item.type;

				let text = iframeDocument.createTextNode('+');
				plusElement.appendChild(text);

				iframeDocument.querySelector(item.container).appendChild(plusElement);
				plusElement.addEventListener('mouseenter', onPlusElement);
			}
			items.forEach(element => {
				element.addEventListener('mouseenter', onEditableElement);
			});
		});
		iframeOverlay = new IframeOverlay(d.getElementById('preview-techletter'), (reference, event) => {
			linkEditor
				.updateLink(reference)
				.then(newdata => {
					notyf.confirm('Mise à jour en cours');
					updatePreview();
				})
				.catch(reason => {
					notyf.alert('Erreur lors de la mise à jour des données : ' + reason);
					console.log('error in promise', reason);
				});
			;
		}, 'Editer');
		iframePlusOverlay = new IframeOverlay(d.getElementById('preview-techletter'), (reference, event) => {
			linkEditor
				.addLink(reference)
				.then(newdata => {
					notyf.confirm('Mise à jour en cours');
					updatePreview();
				})
				.catch(reason => {
					notyf.alert('Erreur lors de la mise à jour des données : ' + reason);
					console.log('error in promise', reason);
				})
			;

		}, 'Ajouter');
	};

	let onPreviewLoaded = function(event)
	{
		this.style.height =	this.contentWindow.document.body.offsetHeight + 'px';
		declareListeners(this.contentWindow.document);
		this.style.height =	this.contentWindow.document.body.offsetHeight + 'px';
	};

	d.getElementById('preview-techletter').onload = onPreviewLoaded;

})(window, document);
