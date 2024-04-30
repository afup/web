$ = jQuery;

$(document).ready(function(){
    'use strict';

    let nbMaxPersonnes = $('#divPersonne').data('nb-max-personnes');
    let isSubjectedToVat = $('#ticketing').data('is-subjected-to-vat') == 1;
    let hasPricesDefinedWithVat = $('#ticketing').data('has-prices-defined-with-vat') == 1;

    let vatRate = parseFloat($('#ticketing').data('vat-rate'))

    let computeWithoutTaxesPriceFromPriceWithTaxes = function (price) {
        return price / (1 + vatRate);
    }

    let computeWithTaxesPriceFromPriceWithoutTaxes = function (price) {
        return price * (1 + vatRate);
    }

    let computeWithoutTaxesPriceFromPriceWithTaxesConditionally = function (price) {
        if (!isSubjectedToVat) {
            return price;
        }

        return computeWithoutTaxesPriceFromPriceWithTaxes(price);
    }

    let computeWithTaxesPriceFromPriceWithoutTaxesConditionally = function (price) {
        if (!isSubjectedToVat) {
            return price;
        }

        return computeWithTaxesPriceFromPriceWithoutTaxes(price);
    }

    let formatPrice = function (price) {
        let formatter = Intl.NumberFormat(
            'fr-FR',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }
        )

        return formatter.format(price);
    }

    // Check if there is some saved data in LocalStorage
	var storageAvailable = function (type) {
		try {
			var storage = window[type],
				x = '__storage_test__';
			storage.setItem(x, x);
			storage.removeItem(x);
			return true;
		}
		catch(e) {
			return false;
		}
	}

    var manageFieldSet = function (nbInscriptions) {
        for (var i = 1; i < (nbMaxPersonnes - 1); i++) {
            $('fieldset.tickets--fieldset').attr('style','display:none !important');
        }

        for (var i = 1; i < (nbInscriptions + 1); i++) {
            $('fieldset.f' + i).attr('style','display:block !important');
            $('fieldset.f' + i).find('input[data-required=true]').attr('required', true);
			if (typeof $('input[name="purchase[tickets][' + i +'][ticketType]"]:checked').val() === "undefined") {
			    $('fieldset.f' + i).find('ul.tickets--type-list input[type=radio]:not(:disabled):first').attr('checked', true);
			}

            updateFieldsetSummary($('fieldset.f' + i));
        }

        updateFieldsetSummary($('.fieldset-facturation'));

        if (nbInscriptions === nbMaxPersonnes) {
            $('a.add_inscription').attr('disabled', 'true');
        } else {
            $('a.add_inscription').removeAttr('disabled');
        }
    }

    var checkFieldSet = function (fieldset, onSuccess, onFailure) {
        // Check validity for every field
        var validity = true;
        fieldset.find('input').each(function(){
            validity &= this.checkValidity();
        });

        if (validity == true) {
            // Hide current fieldset
            $(this).parents('div.fieldset--inner').hide('slow');

            if (typeof onSuccess === 'function') {
                onSuccess.call(this);
            }
        } else if (typeof onFailure === 'function') {
            onFailure.call(this);
        }
    }

    var parseUri = function (uri) {
        var a = document.createElement('a');
        a.href = uri;
        return a.pathname;
    }
    $('#divPersonne').show();

    var updateSummary = function () {
        var inscriptions = {};
        for (var i = 1; i <= nbInscriptions; i++) {
            var fieldset = $('fieldset.f' + i);
            var radio = fieldset.find('ul.tickets--type-list input[type=radio]:checked');
            var price = radio.data('price');
            var label = radio.data('label');

            if (typeof inscriptions[label] === 'undefined') {
                inscriptions[label] = {price: price, quantity: 1};
            } else {
                inscriptions[label].quantity = inscriptions[label].quantity + 1;
            }
            inscriptions[label].subtotal = inscriptions[label].quantity * inscriptions[label].price;
        }

        var table = document.createElement('table');
        var numberOfTickets = 0;
        var total = 0;

        var tr = document.createElement('tr');
        var th = document.createElement('th');
        var td = document.createElement('td');

        var df = document.createDocumentFragment();

        if (isSubjectedToVat) {
            var trClone = tr.cloneNode();
            trClone.classList.add('registration')
            var thClone = th.cloneNode();
            thClone.appendChild(document.createTextNode("Type"));
            trClone.appendChild(thClone);

            var tdClone = td.cloneNode();
            tdClone.setAttribute('class', 'text-align-right');
            tdClone.appendChild(document.createTextNode("Prix HT Unitaire"));
            trClone.appendChild(tdClone);

            var tdClone = td.cloneNode();
            tdClone.setAttribute('class', 'text-align-right');
            tdClone.appendChild(document.createTextNode("Quantité"));
            trClone.appendChild(tdClone);

            var tdClone = td.cloneNode();
            tdClone.setAttribute('class', 'text-align-right');
            tdClone.appendChild(document.createTextNode("Prix HT"));
            trClone.appendChild(tdClone);

            var tdClone = td.cloneNode();
            tdClone.appendChild(document.createTextNode("Prix TTC"));
            trClone.appendChild(tdClone);

            df.appendChild(trClone);
        }

        for (var i in inscriptions) {
            var trClone = tr.cloneNode();
            trClone.classList.add('registration')
            var thClone = th.cloneNode();
            thClone.appendChild(document.createTextNode(i));
            trClone.appendChild(thClone);

            var tdClone = td.cloneNode();
            tdClone.setAttribute('class', 'text-align-right');
            tdClone.appendChild(document.createTextNode(formatPrice(hasPricesDefinedWithVat ? computeWithoutTaxesPriceFromPriceWithTaxesConditionally(inscriptions[i].price) : inscriptions[i].price) + '€'));
            trClone.appendChild(tdClone);

            var tdClone = td.cloneNode();
            tdClone.setAttribute('class', 'text-align-right');
            tdClone.appendChild(document.createTextNode('x' + inscriptions[i].quantity));
            trClone.appendChild(tdClone);

            var tdClone = td.cloneNode();
            tdClone.setAttribute('class', 'text-align-right');
            tdClone.appendChild(document.createTextNode(formatPrice(hasPricesDefinedWithVat ? computeWithoutTaxesPriceFromPriceWithTaxesConditionally(inscriptions[i].subtotal) : inscriptions[i].subtotal) + '€'));
            trClone.appendChild(tdClone);

            if (isSubjectedToVat) {
                var tdClone = td.cloneNode();
                tdClone.appendChild(document.createTextNode(formatPrice(hasPricesDefinedWithVat ? inscriptions[i].subtotal : computeWithTaxesPriceFromPriceWithoutTaxes(inscriptions[i].subtotal)) + '€'));
                trClone.appendChild(tdClone);
            }

            df.appendChild(trClone);
            numberOfTickets += inscriptions[i].quantity;
            total += inscriptions[i].subtotal;
        }

        var trClone = tr.cloneNode();
        var thClone = th.cloneNode();
        thClone.appendChild(document.createTextNode('Total :'));
        trClone.appendChild(thClone);

        var tdClone = td.cloneNode();
        tdClone.appendChild(document.createTextNode(''));
        trClone.appendChild(tdClone);

        var tdClone = td.cloneNode();
        tdClone.setAttribute('class', 'text-align-right');
        tdClone.appendChild(document.createTextNode('x' + numberOfTickets));
        trClone.appendChild(tdClone);

        var tdClone = td.cloneNode();
        tdClone.setAttribute('class', 'text-align-right');
        tdClone.appendChild(document.createTextNode(formatPrice(computeWithoutTaxesPriceFromPriceWithTaxesConditionally(total)) + '€'));
        trClone.appendChild(tdClone);

        if (isSubjectedToVat) {
            var tdClone = td.cloneNode();
            tdClone.setAttribute('class', 'text-align-right');
            tdClone.appendChild(document.createTextNode(formatPrice(total) + '€'));
            trClone.appendChild(tdClone);
        }

        df.appendChild(trClone);

        table.appendChild(df);

        // Empty the current summary
        var myNode = document.getElementById("summary");
        while (myNode.firstChild) {
            myNode.removeChild(myNode.firstChild);
        }

        $('#summary').append(table);
    }

    var updateFieldsetSummary = function(fieldset) {
		var lastname = fieldset.find('input[name$="[lastname]"]').val();
		var firstname = fieldset.find('input[name$="[firstname]"]').val();

		$(fieldset).find('legend span.fieldset--legend--title').html(' - ' + firstname + ' ' + lastname);

		if (fieldset.hasClass('fieldset-facturation') === true) {
			var paymentId = fieldset.find('input[type=radio]:checked').attr('id');
			var paymentId = fieldset.find('input[type=radio]:checked').attr('id');
			$(fieldset).find('legend span.fieldset--legend--price').html($('label[for=' + paymentId + ']').html());
		} else {
			var price = fieldset.find('ul.tickets--type-list input[type=radio]:checked').data('price');
			if (typeof price !== 'undefined') {
                var vatSuffix = hasPricesDefinedWithVat ? 'TTC': 'HT';
                $(fieldset).find('legend span.fieldset--legend--price').html(price + '€' + (isSubjectedToVat ? ' ' + vatSuffix : ''));
            }
		}
    }

    $('a.add_inscription').click(function (event) {
        event.preventDefault();
        // Add data to fieldset legend
        var fieldset = $(this).parents('fieldset').first();

        checkFieldSet.call(this, fieldset, function(){
            // Go to the next inscription
            var nextRegistration = parseInt($(this).data('registration')) + 1;

            var nbPersonnes = parseInt($('#purchase_nbPersonnes').val(), 10);
            if (nbPersonnes < nbMaxPersonnes && nextRegistration > nbPersonnes) {
                $('#purchase_nbPersonnes').val(nextRegistration);
            }
            $('#purchase_nbPersonnes').change();
            manageFieldSet(nextRegistration);
        });
    });

    $('a.fieldset--link-facturation').click(function(event){
        // Add data to fieldset legend
        var link = $(this);

        var fieldset = $(this).parents('fieldset').first();
        checkFieldSet.call(this,
            fieldset,
            function(){link.attr('href', '#facturation');},
            function(){link.attr('href', '#' + fieldset.find('legend a:first').attr('name'));}
        );
        $('fieldset.f6 div.fieldset--inner').show('slow');
    });

    $("#purchase_nbPersonnes").change(function () {
        var nb = parseInt($("#purchase_nbPersonnes").val(), 10);

        if (storageAvailable('localStorage')) {
			localStorage.setItem('nbPersonnes', $(this).val());
		}

        nbInscriptions = nb;
        manageFieldSet(nb);
    });

    $("legend").click(function(event){
        $('div.fieldset--inner')
            .not($(this).parents('fieldset').find('div.fieldset--inner'))
            .not('.f8') // this is broken
            .hide('slow')
        ;
        $(this).parents('fieldset').find('div.fieldset--inner').toggle('slow');
    });

    // We handle validation here
    document.getElementById('formulaire').noValidate = true;

    $('#formulaire').on('submit', function(event){
        $(this).attr('disabled', 'disabled');

        var fieldsets = ['f' + nbMaxPersonnes +1, 'f' + nbMaxPersonnes + 2];

        for (var i = 1; i <= nbInscriptions; i++) {
			fieldsets.push('f' + i);
		}

		for (var set in fieldsets) {
			$('fieldset.' + fieldsets[set]).each(function(){
				var validity = true;
				$(this).find('input').each(function(){
					validity &= this.checkValidity();
				});
				if (validity == false) {
					$(this).find('div.fieldset--inner').show();
					event.preventDefault();
				} else if(this.classList.contains('f8') === false) {
					$(this).find('div.fieldset--inner').hide();
				}
			});
		}

		if (storageAvailable('localStorage')) {
			localStorage.removeItem('tickets');
			localStorage.removeItem('nbPersonnes');
		}
    });

    $('#formulaire input').on('change', function (event) {
        var formData = new FormData(document.querySelector('#formulaire'));
        var data = {};

		var form = formData.entries();
		var obj = form.next();
		var data = {};

		// Si on etait en ES6 on pourrait utiliser `for [key, value] of formData.entries()` mais bon c'est encore tot
		while(undefined !== obj.value) {
			data[obj.value[0]] = obj.value[1];
			obj = form.next();
		}

        if (storageAvailable('localStorage')) {
            localStorage.setItem('tickets', JSON.stringify(data));
        }

        updateSummary();
    });

    $('.tickets--fieldset input[name$="[lastname]"],.tickets--fieldset input[name$="[firstname]"],.tickets--fieldset ul.tickets--type-list input[type=radio]').on('change', function(){
        var fieldset = $(this).parents('fieldset').first();
        updateFieldsetSummary(fieldset);
    });

    $('.fieldset-facturation input[name$="[lastname]"],.fieldset-facturation input[name$="[firstname]"],.fieldset-facturation input[type=radio]').on('change', function() {
        updateFieldsetSummary($('.fieldset-facturation'));
    });

    $('#tickets--other-payments').click(function(event){
    	event.preventDefault();
    	$('.tickets--bankwire').toggle('slow');
	});

    var copyValBetweenFields = function (fromField, toField) {
        $('#' + toField).val ($('#' + fromField).val());
		$('#' + toField).change();
    }

    // Auto-fill Billing from first ticket information
    $('#purchase_tickets_0_lastname').on('change', function(){copyValBetweenFields('purchase_tickets_0_lastname', 'purchase_lastname')});
    $('#purchase_tickets_0_firstname').on('change', function(){copyValBetweenFields('purchase_tickets_0_firstname', 'purchase_firstname')});
    $('#purchase_tickets_0_email').on('change', function(){copyValBetweenFields('purchase_tickets_0_email', 'purchase_email')});

    var init = function(){
		if (storageAvailable('localStorage') && localStorage.getItem('tickets')) {
			try {
				var data = localStorage.getItem('tickets');
				if (data !== null) {
					var tickets = JSON.parse(data);
				}
				nbInscriptions = parseInt(localStorage.getItem('nbPersonnes'));
			} catch (e) {
				// There is an error in the value stored, we remove it to prevent any errors
				localStorage.removeItem('tickets');
				localStorage.removeItem('nbPersonnes');
				tickets = null;
			}
		}

		if (tickets !== null) {
			for (var field in tickets) {
				var value = tickets[field];
				if (value !== '') {
					$('input[type!=radio][name="' + field + '"],select[name="' + field + '"],textarea[name="' + field + '"]').val(value);
					$('input[name="' + field + '"][value="' +value + '"]').attr('checked', 'checked');
				}
			}
		}

		$("#purchase_nbPersonnes option[value=" + nbInscriptions + "]").attr('selected', 'selected').change();

		manageFieldSet(nbInscriptions);

		updateSummary();
    };
    init();

})
