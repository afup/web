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
            var lastname = fieldset.find('input[name^="nom"]').val();
            var firstname = fieldset.find('input[name^="prenom"]').val();

            $(fieldset).find('legend span.fieldset--legend--title').html(' - ' + firstname + ' ' + lastname);

            var price = fieldset.find('input[name^="type_inscription"]:checked').data('price');

            $(fieldset).find('legend span.fieldset--legend--price').html(price + '€');

            // Hide current fieldset
            $(this).parents('div.fieldset--inner').hide('slow');

            if (typeof onSuccess === 'function') {
                onSuccess();
            }
        } else if (typeof onFailure === 'function') {
            onFailure();
        }
    }

    manageFieldSet(nbInscriptions);

    var parseUri = function (uri) {
        var a = document.createElement('a');
        a.href = uri;
        return a.pathname;
    }
    $('#divPersonne').show();

    $('a.add_inscription').click(function (event) {
        event.preventDefault();

        // Add data to fieldset legend
        var fieldset = $(this).parents('fieldset').first();
        checkFieldSet.call(this, fieldset, function(){
            // Add another inscription
            var nbPersonnes = parseInt($('#nbPersonnes').val(), 10);
            if (nbPersonnes < 5) {
                $('#nbPersonnes').val(nbPersonnes + 1);
                $('#nbPersonnes').change();
            }
            manageFieldSet(nbPersonnes + 1);
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

        manageFieldSet(nb);
    });

    $("legend").click(function(event){
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
            } else {
                $(this).find('div.fieldset--inner').hide();
            }
        });
    });

    var copyValBetweenFields = function(fromField, toField) {
        $('input[name="' + toField + '"]').val ($('input[name="' + fromField + '"]').val());
    }

    // Auto-fill Billing from first ticket information
    $('fieldset.f1 input[name=nom1]').on('blur', function(){copyValBetweenFields('nom1', 'nom_facturation')});
    $('fieldset.f1 input[name=prenom1]').on('blur', function(){copyValBetweenFields('prenom1', 'prenom_facturation')});
    $('fieldset.f1 input[name=email1]').on('blur', function(){copyValBetweenFields('email1', 'email_facturation')});
})
