$ = jQuery;

$(document).ready(function(){
    'use strict';

    $("#nbPersonnes option[value=" + nbInscriptions + "]").attr('selected', 'selected');

    var manageFieldSet = function (nbInscriptions) {
        for (var i = 1; i < 6; i++) {
            $('fieldset.f' + i).hide();
        }

        for (var i = 1; i < (nbInscriptions + 1); i++) {
            $('fieldset.f' + i).show();
            $('fieldset.f' + i).find('input[data-required=true]').attr('required', true);
            $('fieldset.f' + i).find('input[name^="type_inscription"]:first').attr('checked', true);
        }

        if (nbInscriptions === 5) {
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

    manageFieldSet(nbInscriptions);

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
            var radio = fieldset.find('input[name^="type_inscription"]:checked');
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

        for (var i in inscriptions) {
            var trClone = tr.cloneNode();
            trClone.classList.add('registration')
            var thClone = th.cloneNode();
            thClone.appendChild(document.createTextNode(i));
            trClone.appendChild(thClone);

            var tdClone = td.cloneNode();
            tdClone.appendChild(document.createTextNode(inscriptions[i].price + '€'));
            trClone.appendChild(tdClone);

            var tdClone = td.cloneNode();
            tdClone.appendChild(document.createTextNode('x' + inscriptions[i].quantity));
            trClone.appendChild(tdClone);

            var tdClone = td.cloneNode();
            tdClone.appendChild(document.createTextNode(inscriptions[i].subtotal + '€'));
            trClone.appendChild(tdClone);

            df.appendChild(trClone);
            numberOfTickets += inscriptions[i].quantity;
            total += inscriptions[label].subtotal;
        }

        var trClone = tr.cloneNode();
        var thClone = th.cloneNode();
        thClone.appendChild(document.createTextNode('Total :'));
        trClone.appendChild(thClone);

        var tdClone = td.cloneNode();
        tdClone.appendChild(document.createTextNode(''));
        trClone.appendChild(tdClone);

        var tdClone = td.cloneNode();
        tdClone.appendChild(document.createTextNode('x' + numberOfTickets));
        trClone.appendChild(tdClone);

        var tdClone = td.cloneNode();
        tdClone.appendChild(document.createTextNode(total + '€'));
        trClone.appendChild(tdClone);

        df.appendChild(trClone);

        table.appendChild(df);

        // Empty the current summary
        var myNode = document.getElementById("summary");
        while (myNode.firstChild) {
            myNode.removeChild(myNode.firstChild);
        }

        $('#summary').append(table);
    }

    updateSummary();

    $('a.add_inscription').click(function (event) {
        event.preventDefault();

        // Add data to fieldset legend
        var fieldset = $(this).parents('fieldset').first();

        checkFieldSet.call(this, fieldset, function(){
            // Go to the next inscription
            var nextRegistration = parseInt($(this).data('registration')) + 1;

            var nbPersonnes = parseInt($('#nbPersonnes').val(), 10);
            if (nbPersonnes < 5 && nextRegistration > nbPersonnes) {
                $('#nbPersonnes').val(nextRegistration);
            }
            $('#nbPersonnes').change();
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

    $("#nbPersonnes").change(function () {
        var nb = parseInt($("#nbPersonnes").val(), 10);
        var path = parseUri($("#formulaire").attr("action"));
        if (path.substr(0, 1) != '/') {
            path = '/' + path;
        }

        var junction = '?';
        if (path.indexOf('?') > -1) {
            junction = '&';
        }

        $("#formulaire").attr("action", path + junction + 'nbInscriptions=' + nb);
        nbInscriptions = nb;

        manageFieldSet(nb);
    });

    $("legend").click(function(event){
        $('div.fieldset--inner')
            .not($(this).parents('fieldset').find('div.fieldset--inner'))
            .not('#fieldset--7')
            .hide('slow')
        ;
        $(this).parents('fieldset').find('div.fieldset--inner').toggle('slow');
    });

    // We handle validation here
    document.getElementById('formulaire').noValidate = true;

    $('#formulaire').on('submit', function(event){
        $(this).attr('disabled', 'disabled');

        $(this).find('fieldset').each(function(){
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
    });

    $('#formulaire input').on('change', function (event) {
        updateSummary();
    });

    $('input[name^="nom"],input[name^="prenom"],input[name^="type_inscription"]').on('change', function(){
        var fieldset = $(this).parents('fieldset').first();
        var lastname = fieldset.find('input[name^="nom"]').val();
        var firstname = fieldset.find('input[name^="prenom"]').val();

        $(fieldset).find('legend span.fieldset--legend--title').html(' - ' + firstname + ' ' + lastname);

        if (fieldset.hasClass('f6') === true) {
            var paymentId = fieldset.find('input[name="type_reglement"]:checked').attr('id');
            $(fieldset).find('legend span.fieldset--legend--price').html($('label[for=' + paymentId + ']').html());
        } else {
            var price = fieldset.find('input[name^="type_inscription"]:checked').data('price');
            $(fieldset).find('legend span.fieldset--legend--price').html(price + '€');
        }
    });

    var copyValBetweenFields = function (fromField, toField) {
        $('input[name="' + toField + '"]').val ($('input[name="' + fromField + '"]').val());
    }

    // Auto-fill Billing from first ticket information
    $('fieldset.f1 input[name=nom1]').on('change', function(){copyValBetweenFields('nom1', 'nom_facturation')});
    $('fieldset.f1 input[name=prenom1]').on('change', function(){copyValBetweenFields('prenom1', 'prenom_facturation')});
    $('fieldset.f1 input[name=email1]').on('change', function(){copyValBetweenFields('email1', 'email_facturation')});

    // Select radio button
    $('input[name=type_reglement]:first').attr('checked', 'checked');
    $('input[name=citer_societe]:eq(0)').attr('checked', 'checked');
    $('input[name=newsletter_afup]:eq(1)').attr('checked', 'checked');
})
