'use strict';

let getModelForType = (type) => {
	let model = editableElements.find(item => {
		return item.type === type;
	});
	if (typeof model !== "undefined") {
		return model.model;
	}
	return false; // or throw error ?
};

/**
 *
 * @param oldLink
 * @param newData if null, will remove the entry
 * @returns {boolean}
 */
let updateDataForLink = function (oldLink, newData) {
	if (techletter.firstNews && techletter.firstNews.url === oldLink) {
		if (newData === null) {
			techletter.firstNews = techletter.secondNews;
			techletter.secondNews = null;
		} else {
			techletter.firstNews = newData;
		}
		return true;
	}
	if (techletter.secondNews && techletter.secondNews.url === oldLink) {
		techletter.secondNews = newData;
		return true;
	}

	for (let index in techletter.articles) {
		if (techletter.articles[index].url === oldLink) {
			if (newData === null) {
				techletter.articles.splice(index, 1);
			} else {
				techletter.articles[index] = newData;
			}
			return true;
		}
	}

	for (let index in techletter.projects) {
		if (techletter.projects[index].url === oldLink) {
			if (newData === null) {
				techletter.projects.splice(index, 1);
			} else {
				techletter.projects[index] = newData;
			}
			return true;
		}
	}

	return false;
};

let getDetailsForLink = function (link) {
	if (techletter.firstNews && techletter.firstNews.url === link) {
		return {model: "news", data: techletter.firstNews};
	}
	if (techletter.secondNews && techletter.secondNews.url === link) {
		return {model: "news", data: techletter.secondNews};
	}
	let article = techletter.articles.find(item => {
		if (item.url === link) {
			return true;
		}
	});
	if (typeof article !== "undefined") {
		return {model: "articles", data: article};
	}
	let project = techletter.projects.find(item => {
		if (item.url === link) {
			return true;
		}
	});
	if (project !== "undefined") {
		return {model: "projects", data: project};
	}
	return false; // or throw error ?
};


let LinkEditor = {};
LinkEditor = function (form) {
	this.form = form;
	this.fieldset = form.querySelector('fieldset');
	this.retrievePath = this.form.dataset.refresh;

	// Add Listener on submit for this form, get new data, resolve promise
	this.form.addEventListener('submit', this.handleSubmit.bind(this));
	this.form.addEventListener('reset', this.handleReset.bind(this));
	this.form.querySelector('button#refresh').addEventListener('click', this.handleRefresh.bind(this));
	this.form.querySelector('button#delete').addEventListener('click', this.handleDelete.bind(this));
	this.form.querySelector('#close').addEventListener('click', this.handleReset.bind(this));
	this.form.querySelector('button#up').addEventListener('click', this.handleUp.bind(this));
	this.form.querySelector('button#down').addEventListener('click', this.handleDown.bind(this));
};

LinkEditor.prototype = {
	form: null,
	fieldset: null,
	resolve: null,
	reject: null,
	retrievePath: null,

	/**
	 *
	 * @param model
	 * @param data if null, we will create a blank form
	 */
	createForm: function (model, data) {
		// Remove all children of the fieldset
		while (this.fieldset.firstChild) {
			this.fieldset.removeChild(this.fieldset.firstChild);
		}

		if (data !== null) {
			this.fieldset.dataset.link = data.url;
		}

		Object.keys(model).forEach((key, index) => {
			let label = document.createElement('label');
			let text = document.createTextNode(
				key
					.replace(/([A-Z])/g, str => {return ' ' + str.toLowerCase();})
					.replace(/^./, str => {return str.toUpperCase();})
			);
			label.appendChild(text);
			label.setAttribute('for', `input-${key}`);

			let input = document.createElement(model[key].inputType);
			input.setAttribute('id', `input-${key}`);
			input.dataset.key = key;

			if (data !== null) {
				let value = data[key];
				input.value = value;
			}

			Object.keys(model[key]).forEach((inputOption, index) => {
				input[inputOption] = model[key][inputOption];
			});

			if (model[key].inputType === "select" && typeof model[key].values !== 'undefined') {
                Object.keys(model[key].values).forEach((modelIndex) => {
                	let option = document.createElement('option');
                	var modelValue = model[key].values[modelIndex];
                	option.setAttribute('value', modelIndex);
                	if (data !== null && modelIndex === data[key]) {
                        option.setAttribute('selected', 'selected');
					}
					option.innerHTML = modelValue;
                    input.appendChild(option);
                });
			}


			let div = document.createElement('div');
			div.appendChild(label);
			div.appendChild(input);

			this.fieldset.appendChild(div);
		});
	},

	updateLink: function (link) {
		return new Promise((resolve, reject) => {
			this.resolve = resolve;
			this.reject = reject;

			// Create fields for the data
			let details = getDetailsForLink(link);
			let model = getModelForType(details.model);

			this.fieldset.dataset.type = details.model;

			this.createForm(model, details.data);
			this.form.classList.remove('hidden');

			this.form.querySelector('button#up').classList.remove('hidden');
			this.form.querySelector('button#down').classList.remove('hidden');
		});
	},

	addLink: function (type) {
		return new Promise((resolve, reject) => {
			this.resolve = resolve;
			this.reject = reject;

			// Create fields for the data
			let model = getModelForType(type);

			this.createForm(model, null);
			this.form.classList.remove('hidden');

			// On supprime les boutons "remonter" et "descendre" pour les liens
			this.form.querySelector('button#up').classList.add('hidden');
			this.form.querySelector('button#down').classList.add('hidden');

			this.fieldset.dataset.type = type;
			delete this.fieldset.dataset.link;
		});
	},

	handleSubmit: function(event) {
		event.preventDefault();
		let data = [].reduce.call(this.form.elements, (data, element) => {
			if ("key" in element.dataset) {
				data[element.dataset.key] = element.value;
			}
			return data;
		}, {});


		if ("link" in this.fieldset.dataset) {
			updateDataForLink(this.fieldset.dataset.link, data);
		} else {
			if (this.fieldset.dataset.type === "news") {
				if (techletter.firstNews === null) {
					techletter.firstNews = data;
				} else {
					techletter.secondNews = data;
				}
			} else {
				techletter[this.fieldset.dataset.type].push(data);
			}
			this.resolve(data);
			this.form.classList.add('hidden');
			this.form.reset();

			this.updateLink(data.url);
		}

		updatePreview();
	},

	handleReset: function (event) {
		event.preventDefault();
		this.form.classList.add('hidden');
		this.resolve(techletter); // Data has been updated so we can resolve the promise
	},

	handleDelete: function (event) {
		event.preventDefault();
		if ("link" in this.fieldset.dataset) {
			updateDataForLink(this.fieldset.dataset.link, null);
		}
		updatePreview();
		this.form.classList.add('hidden');
		this.resolve(techletter); // Data has been updated so we can resolve the promise
	},

	handleUp: function(event) {
		event.preventDefault();

		this.up();

		updatePreview();
		this.resolve(techletter); // Data has been updated so we can resolve the promise
	},

	handleDown: function(event) {
		event.preventDefault();

		this.down();

		updatePreview();
		this.resolve(techletter); // Data has been updated so we can resolve the promise
	},

	handleRefresh: function (event) {
		event.preventDefault();
		this.lock();

		fetch (this.retrievePath, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
			},
			body: 'url=' + encodeURIComponent(this.fieldset.querySelector('#input-url').value)
		})
		.then((response) => response.json())
			.then(json => {
				Object.keys(json).forEach(key => {
					let input = this.form.querySelector(`#input-${key}`);
					if (input !== null) {
						input.value = json[key];
					}
				});
				// @todo move to an event
				notyf.confirm('Mise à jour du lien effectuée');
				this.unlock();
			})
			.catch(error => {
				// @todo move to an event
				notyf.alert('Erreur lors de la mise à jour: ' + error);
				this.unlock();
			})
		;
	},

	lock: function () {
		this.fieldset.disabled = "disabled";
	},

	unlock: function () {
		this.fieldset.disabled = "";
	},

	up: function () {
		let actualIndex = this.getLinkIndex();
		// News
		if (this.fieldset.dataset.type === 'news') {
			if (actualIndex === 1) {
				const tmp = techletter.firstNews;
				techletter.firstNews = techletter.secondNews;
				techletter.secondNews = tmp;
			}
		} else {
			// Other types (array)
			let data = techletter[this.fieldset.dataset.type];
			if (actualIndex > -1 && data.length > 1) {
                const newIndex = (actualIndex > 1) ? actualIndex-1 : 0;
                if (newIndex < actualIndex) {
                    data = data.slice(0, actualIndex-1).concat(data[actualIndex], data[newIndex], data.slice(newIndex+2));
                }
            }
			techletter[this.fieldset.dataset.type] = data;
		}
	},

	down: function () {
		let actualIndex = this.getLinkIndex();
		if (this.fieldset.dataset.type === 'news') {
			if (actualIndex === 0) {
				const tmp = techletter.firstNews;
				techletter.firstNews = techletter.secondNews;
				techletter.secondNews = tmp;
			}
		} else {
			// Other types (array)
			let data = techletter[this.fieldset.dataset.type];
			if (actualIndex > -1 && data.length > 1) {
				let newIndex = (actualIndex < data.length-1) ? actualIndex+1 : data.length-1;
				if (actualIndex < newIndex) {
					data = data.slice(0, actualIndex).concat(data[newIndex], data[actualIndex], data.slice(newIndex+1));
				}
			}
			techletter[this.fieldset.dataset.type] = data;
		}
	},

	getLinkIndex: function () {
		if (this.fieldset.dataset.url !== 'undefined') {
			if (this.fieldset.dataset.type === 'news') {
				return (techletter.firstNews.url === this.fieldset.dataset.link) ? 0 : 1;
			} else {
				let data = techletter[this.fieldset.dataset.type];
				for (let i = 0; i < data.length; i++) {
					if (data[i].url === this.fieldset.dataset.link) {
						return i;
					}
				}
			}
		}
		return -1;
	}
};
export { LinkEditor };
