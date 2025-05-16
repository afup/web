$ = jQuery;

$(document).ready(function() {
    'use strict';

    // Initialize variables
    let currentStep = 1;
    let nbMaxPersonnes = $('#divPersonne').data('nb-max-personnes');
    let isSubjectedToVat = $('#ticketing').data('is-subjected-to-vat') == 1;
    let hasPricesDefinedWithVat = $('#ticketing').data('has-prices-defined-with-vat') == 1;
    let vatRate = parseFloat($('#ticketing').data('vat-rate'));
    let nbInscriptions = parseInt($('#purchase_nbPersonnes').val(), 10);

    // VAT calculation functions
    const computeWithoutTaxesPriceFromPriceWithTaxes = function(price) {
        return price / (1 + vatRate);
    }

    const computeWithTaxesPriceFromPriceWithoutTaxes = function(price) {
        return price * (1 + vatRate);
    }

    const computeWithoutTaxesPriceFromPriceWithTaxesConditionally = function(price) {
        if (!isSubjectedToVat) {
            return price;
        }
        return computeWithoutTaxesPriceFromPriceWithTaxes(price);
    }

    const computeWithTaxesPriceFromPriceWithoutTaxesConditionally = function(price) {
        if (!isSubjectedToVat) {
            return price;
        }
        return computeWithTaxesPriceFromPriceWithoutTaxes(price);
    }

    const formatPrice = function(price) {
        let formatter = Intl.NumberFormat(
            'fr-FR',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }
        )
        return formatter.format(price);
    }

    // LocalStorage helper functions
    const storageAvailable = function(type) {
        try {
            var storage = window[type],
                x = '__storage_test__';
            storage.setItem(x, x);
            storage.removeItem(x);
            return true;
        } catch(e) {
            return false;
        }
    }

    // Update steps indicator
    const updateStepsIndicator = function(step) {
        // Update current step attribute
        $('.steps-indicator').attr('data-current-step', step);
        
        // Reset all step classes
        $('.step-item').removeClass('active completed');
        
        // Mark completed steps
        for (let i = 1; i < step; i++) {
            $(`.step-item[data-step="${i}"]`).addClass('completed');
        }
        
        // Mark current step as active
        $(`.step-item[data-step="${step}"]`).addClass('active');
    }

    // Navigation
    const goToStep = function(step) {
        // Validate current step before proceeding
        if (step > currentStep && !validateStep(currentStep)) {
            return false;
        }

        // Hide all steps and show the requested one
        $('.ticket-step').removeClass('active');
        $(`#ticket-step-${step}`).addClass('active');

        // Update steps indicator
        updateStepsIndicator(step);

        // Update current step
        currentStep = step;

        // Scroll to top of the form
        $('html, body').animate({
            scrollTop: $(".steps-indicator").offset().top - 20
        }, 300);

        return true;
    }

    // Step validation
    const validateStep = function(step) {
        let valid = true;

        switch(step) {
            case 1:
                // Validate number of tickets
                const nbPersonnes = parseInt($('#purchase_nbPersonnes').val(), 10);
                if (nbPersonnes < 1 || nbPersonnes > nbMaxPersonnes) {
                    valid = false;
                    alert('Veuillez sélectionner un nombre de billets valide.');
                }
                break;

            case 2:
                // Validate all ticket information
                for (let i = 0; i < nbInscriptions; i++) {
                    // Check if ticket type is selected
                    if ($(`input[name="purchase[tickets][${i}][ticketEventType]"]:checked`).length === 0) {
                        valid = false;
                        alert(`Veuillez sélectionner un type de billet pour la personne ${i+1}.`);
                        break;
                    }

                    // Check required fields
                    const requiredFields = [
                        `purchase[tickets][${i}][firstname]`,
                        `purchase[tickets][${i}][lastname]`,
                        `purchase[tickets][${i}][email]`
                    ];

                    for (const fieldName of requiredFields) {
                        const field = $(`input[name="${fieldName}"]`);
                        if (field.val().trim() === '') {
                            valid = false;
                            field.focus();
                            alert(`Veuillez remplir tous les champs obligatoires pour la personne ${i+1}.`);
                            break;
                        }
                    }

                    if (!valid) break;
                }
                break;

            case 3:
                // Validate billing and payment information
                if ($('input[name="purchase[paymentType]"]:checked').length === 0) {
                    valid = false;
                    alert('Veuillez sélectionner un mode de paiement.');
                    break;
                }

                // Check required fields
                const requiredFields = [
                    'purchase[firstname]',
                    'purchase[lastname]',
                    'purchase[email]',
                    'purchase[address]',
                    'purchase[zipcode]',
                    'purchase[city]'
                ];

                for (const fieldName of requiredFields) {
                    const field = $(`input[name="${fieldName}"]`);
                    if (field.val().trim() === '') {
                        valid = false;
                        field.focus();
                        alert('Veuillez remplir tous les champs obligatoires pour la facturation.');
                        break;
                    }
                }
                break;
        }

        return valid;
    }

    // Manage ticket sections
    const manageTicketSections = function() {
        const nbPersonnes = parseInt($('#purchase_nbPersonnes').val(), 10);
        nbInscriptions = nbPersonnes;

        // Show/hide ticket sections based on number of tickets
        $('.ticket-section').hide();
        for (let i = 0; i < nbPersonnes; i++) {
            $(`#ticket-section-${i}`).show();
            // Ensure required fields are properly marked
            $(`#ticket-section-${i}`).find('input[data-required=true]').attr('required', true);
        }

        // Select first available ticket type for each person if none selected
        for (let i = 0; i < nbPersonnes; i++) {
            if ($(`input[name="purchase[tickets][${i}][ticketEventType]"]:checked`).length === 0) {
                $(`#ticket-section-${i}`).find('ul.ticket-type-list input[type=radio]:not(:disabled):first').prop('checked', true);
            }
        }

        // Update summary
        updateSummary();

        // Store in local storage
        if (storageAvailable('localStorage')) {
            localStorage.setItem('nbPersonnes', nbPersonnes);
        }
    }

    // Update the summary
    const updateSummary = function() {
        var inscriptions = {};
        for (var i = 0; i < nbInscriptions; i++) {
            var radio = $(`input[name="purchase[tickets][${i}][ticketEventType]"]:checked`);
            if (radio.length === 0) continue;
            
            var price = radio.data('price');
            var label = radio.data('label');

            if (typeof inscriptions[label] === 'undefined') {
                inscriptions[label] = {price: price, quantity: 1};
            } else {
                inscriptions[label].quantity = inscriptions[label].quantity + 1;
            }
            inscriptions[label].subtotal = inscriptions[label].quantity * inscriptions[label].price;
        }

        let summaryHtml = '<table class="summary-table">';
        summaryHtml += '<thead><tr>';
        summaryHtml += '<th>Type de billet</th>';
        
        if (isSubjectedToVat) {
            summaryHtml += '<th class="text-right">Prix unitaire HT</th>';
        } else {
            summaryHtml += '<th class="text-right">Prix unitaire</th>';
        }
        
        summaryHtml += '<th class="text-right">Quantité</th>';
        
        if (isSubjectedToVat) {
            summaryHtml += '<th class="text-right">Total HT</th>';
            summaryHtml += '<th class="text-right">Total TTC</th>';
        } else {
            summaryHtml += '<th class="text-right">Total</th>';
        }
        
        summaryHtml += '</tr></thead><tbody>';

        let numberOfTickets = 0;
        let total = 0;

        for (let type in inscriptions) {
            summaryHtml += '<tr>';
            summaryHtml += `<td>${type}</td>`;
            
            if (isSubjectedToVat) {
                summaryHtml += `<td class="text-right">${formatPrice(hasPricesDefinedWithVat ? computeWithoutTaxesPriceFromPriceWithTaxesConditionally(inscriptions[type].price) : inscriptions[type].price)}€</td>`;
            } else {
                summaryHtml += `<td class="text-right">${formatPrice(inscriptions[type].price)}€</td>`;
            }
            
            summaryHtml += `<td class="text-right">x${inscriptions[type].quantity}</td>`;
            
            if (isSubjectedToVat) {
                summaryHtml += `<td class="text-right">${formatPrice(hasPricesDefinedWithVat ? computeWithoutTaxesPriceFromPriceWithTaxesConditionally(inscriptions[type].subtotal) : inscriptions[type].subtotal)}€</td>`;
                summaryHtml += `<td class="text-right">${formatPrice(hasPricesDefinedWithVat ? inscriptions[type].subtotal : computeWithTaxesPriceFromPriceWithoutTaxes(inscriptions[type].subtotal))}€</td>`;
            } else {
                summaryHtml += `<td class="text-right">${formatPrice(inscriptions[type].subtotal)}€</td>`;
            }
            
            summaryHtml += '</tr>';
            
            numberOfTickets += inscriptions[type].quantity;
            total += hasPricesDefinedWithVat ? inscriptions[type].subtotal : computeWithTaxesPriceFromPriceWithoutTaxes(inscriptions[type].subtotal);
        }

        summaryHtml += '<tr class="summary-total">';
        summaryHtml += '<td>Total</td><td></td>';
        summaryHtml += `<td class="text-right">x${numberOfTickets}</td>`;
        
        if (isSubjectedToVat) {
            summaryHtml += `<td class="text-right">${formatPrice(computeWithoutTaxesPriceFromPriceWithTaxesConditionally(total))}€</td>`;
            summaryHtml += `<td class="text-right">${formatPrice(total)}€</td>`;
        } else {
            summaryHtml += `<td class="text-right">${formatPrice(total)}€</td>`;
        }
        
        summaryHtml += '</tr></tbody></table>';

        $('#ticket-summary').html(summaryHtml);
    }

    // Payment options
    const handlePaymentSelection = function() {
        $('.payment-option').click(function() {
            const radioInput = $(this).find('input[type="radio"]');
            radioInput.prop('checked', true);
            $('.payment-option').removeClass('selected');
            $(this).addClass('selected');
        });
    }

    // Auto-fill billing from first ticket
    const setupBillingAutofill = function() {
        $('#purchase_tickets_0_lastname').on('change', function() {
            $('#purchase_lastname').val($(this).val()).change();
        });
        
        $('#purchase_tickets_0_firstname').on('change', function() {
            $('#purchase_firstname').val($(this).val()).change();
        });
        
        $('#purchase_tickets_0_email').on('change', function() {
            $('#purchase_email').val($(this).val()).change();
        });
    }

    // Transport information
    const cloneTransport = function() {
        const $button = $('#clone_transport');
        
        if (nbInscriptions <= 1) {
            $button.hide();
        } else {
            $button.show();
        }

        $button.off('click').on('click', function(e) {
            e.preventDefault();
            const mode = $('#purchase_tickets_0_transportMode').val();
            const distance = $('#purchase_tickets_0_transportDistance').val();
            
            for (let i = 1; i < nbMaxPersonnes; i++) {
                $(`#purchase_tickets_${i}_transportMode`).val(mode).change();
                $(`#purchase_tickets_${i}_transportDistance`).val(distance).change();
            }
            
            return false;
        });
    }

    // Initialize and set up event listeners
    const init = function() {
        // Initialize steps indicator
        updateStepsIndicator(currentStep);
        
        // Set up navigation buttons
        $('.btn-next').click(function(e) {
            e.preventDefault();
            goToStep(currentStep + 1);
        });
        
        $('.btn-prev').click(function(e) {
            e.preventDefault();
            goToStep(currentStep - 1);
        });

        // Set up number of tickets selection
        $('#purchase_nbPersonnes').change(function() {
            manageTicketSections();
            cloneTransport();
        });

        // Set up ticket type selection
        $(document).on('change', 'input[name^="purchase[tickets]"][name$="[ticketEventType]"]', function() {
            updateSummary();
        });

        // Set up payment options
        handlePaymentSelection();

        // Set up billing autofill
        setupBillingAutofill();

        // Restore from localStorage if available
        if (storageAvailable('localStorage')) {
            try {
                const storedTickets = localStorage.getItem('tickets');
                if (storedTickets !== null) {
                    const tickets = JSON.parse(storedTickets);
                    for (const field in tickets) {
                        const value = tickets[field];
                        if (value !== '') {
                            $(`input[type!=radio][name="${field}"],select[name="${field}"],textarea[name="${field}"]`).val(value);
                            $(`input[name="${field}"][value="${value}"]`).prop('checked', true);
                        }
                    }
                }
                
                const storedNbPersonnes = localStorage.getItem('nbPersonnes');
                if (storedNbPersonnes !== null) {
                    nbInscriptions = parseInt(storedNbPersonnes);
                    $(`#purchase_nbPersonnes option[value=${nbInscriptions}]`).prop('selected', true).change();
                }
            } catch (e) {
                // There is an error in the value stored, remove it to prevent errors
                localStorage.removeItem('tickets');
                localStorage.removeItem('nbPersonnes');
            }
        }

        // Initialize form sections
        manageTicketSections();
        cloneTransport();
        updateSummary();

        // Handle form submission
        $('#formulaire').on('submit', function(e) {
            // Validate final step
            if (!validateStep(3)) {
                e.preventDefault();
                return false;
            }

            // Check CGV acceptance
            if (!$('#purchase_cgv').prop('checked')) {
                alert('Vous devez accepter les conditions générales de vente.');
                e.preventDefault();
                return false;
            }

            // Clear localStorage on successful submission
            if (storageAvailable('localStorage')) {
                localStorage.removeItem('tickets');
                localStorage.removeItem('nbPersonnes');
            }
        });

        // Save form data to localStorage on input change
        $('#formulaire input, #formulaire select, #formulaire textarea').on('change', function() {
            if (storageAvailable('localStorage')) {
                const formData = new FormData(document.querySelector('#formulaire'));
                const data = {};

                for (const [key, value] of formData.entries()) {
                    data[key] = value;
                }

                localStorage.setItem('tickets', JSON.stringify(data));
            }
        });
    }

    // Start the initialization
    init();
});