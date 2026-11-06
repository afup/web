document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    const form = document.getElementById('formulaire');
    if (form === null) {
        // Billetterie fermée (tous les billets vendus) : pas de formulaire.
        return;
    }

    const ticketing = document.getElementById('ticketing');
    const nbPersonnesSelect = document.getElementById('purchase_nbPersonnes');

    // Initialize variables
    let currentStep = 1;
    let nbMaxPersonnes = parseInt(document.getElementById('divPersonne').dataset.nbMaxPersonnes, 10);
    let isSubjectedToVat = ticketing.dataset.isSubjectedToVat === '1';
    let hasPricesDefinedWithVat = ticketing.dataset.hasPricesDefinedWithVat === '1';
    let vatRate = parseFloat(ticketing.dataset.vatRate);
    let nbInscriptions = parseInt(nbPersonnesSelect.value, 10);

    // DOM helpers
    const all = function(selector, root = document) {
        return Array.from(root.querySelectorAll(selector));
    }

    const byName = function(name) {
        return form.querySelector(`[name="${name}"]`);
    }

    const checkedTicketType = function(index) {
        return form.querySelector(`input[name="purchase[tickets][${index}][ticketEventType]"]:checked`);
    }

    // Équivalent de `.val(value).change()` : les écouteurs (récapitulatif, localStorage) sont notifiés.
    const setValue = function(field, value) {
        if (field === null) {
            return;
        }
        field.value = value;
        field.dispatchEvent(new Event('change', {bubbles: true}));
    }

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
            const storage = window[type],
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
        document.querySelector('.steps-indicator').dataset.currentStep = step;

        all('.step-item').forEach(function(item) {
            const itemStep = parseInt(item.dataset.step, 10);
            item.classList.toggle('completed', itemStep < step);
            item.classList.toggle('active', itemStep === step);
        });
    }

    // Navigation
    const goToStep = function(step) {
        // Validate current step before proceeding
        if (step > currentStep && !validateStep(currentStep)) {
            return false;
        }

        // Hide all steps and show the requested one
        all('.ticket-step').forEach(function(section) {
            section.classList.toggle('active', section.id === `ticket-step-${step}`);
        });

        // Update steps indicator
        updateStepsIndicator(step);

        // Update current step
        currentStep = step;

        // If we're moving to step 2, update ticket sections to ensure required attributes are correctly set
        if (step === 2) {
            manageTicketSections();
        }

        // Scroll to top of the form
        window.scrollTo({
            top: document.querySelector('.steps-indicator').getBoundingClientRect().top + window.scrollY - 20,
            behavior: 'smooth',
        });

        return true;
    }

    const validateValuesPerFieldSearch = function(fieldSearch, stepName) {
        // Check required fields: inputs first, then selects
        const fields = all(`input[name^="${fieldSearch}"][required]`, form)
            .concat(all(`select[name^="${fieldSearch}"][required]`, form));

        for (const field of fields) {
            if (field.value.trim() === '') {
                field.focus();
                alert(`Veuillez remplir tous les champs obligatoires pour ${stepName}.`);
                return false;
            }
        }
        return true;
    }

    // Step validation
    const validateStep = function(step) {
        let valid = true;

        switch(step) {
            case 1:
                // Validate number of tickets
                const nbPersonnes = parseInt(nbPersonnesSelect.value, 10);
                if (nbPersonnes < 1 || nbPersonnes > nbMaxPersonnes) {
                    valid = false;
                    alert('Veuillez sélectionner un nombre de billets valide.');
                }
                break;

            case 2:
                // Validate ticket information ONLY for the selected number of tickets
                const currentPersonnes = parseInt(nbPersonnesSelect.value, 10);
                for (let i = 0; i < currentPersonnes; i++) {
                    // Check if ticket type is selected
                    if (checkedTicketType(i) === null) {
                        alert(`Veuillez sélectionner un type de billet pour le billet ${i+1}.`);
                        return false;
                    }

                    if (!validateValuesPerFieldSearch(`purchase[tickets][${i}]`, `le billet ${i+1}`)) {
                        return false;
                    }
                }
                break;

            case 3:
                // Validate billing and payment information
                if (form.querySelector('input[name="purchase[paymentType]"]:checked') === null) {
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
                    const field = byName(fieldName);
                    if (field.value.trim() === '') {
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

    // Les champs obligatoires des billets non affichés ne doivent pas bloquer l'envoi du formulaire :
    // on mémorise ceux qui le sont au chargement pour pouvoir le leur rendre.
    const markRequiredTicketFields = function() {
        all('.ticket-section input[required], .ticket-section select[required]').forEach(function(field) {
            field.dataset.required = 'true';
        });
    }

    // Manage ticket sections
    const manageTicketSections = function() {
        const nbPersonnes = parseInt(nbPersonnesSelect.value, 10);
        nbInscriptions = nbPersonnes;

        // First, on page load, mark required fields with data-required attribute if not already done
        markRequiredTicketFields();

        all('.ticket-section').forEach(function(section, i) {
            const visible = i < nbPersonnes;

            // Show/hide ticket sections based on number of tickets
            section.style.display = visible ? '' : 'none';

            // Ensure required fields are properly marked ONLY for visible sections
            all('input, select', section).forEach(function(field) {
                field.required = visible && field.dataset.required === 'true';
            });
        });

        // Select first available ticket type for each person if none selected
        for (let i = 0; i < nbPersonnes; i++) {
            if (checkedTicketType(i) === null) {
                const firstAvailable = document.querySelector(`#ticket-section-${i} ul.ticket-type-list input[type=radio]:not(:disabled)`);
                if (firstAvailable !== null) {
                    firstAvailable.checked = true;
                }
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
        const inscriptions = {};
        for (let i = 0; i < nbInscriptions; i++) {
            const radio = checkedTicketType(i);
            if (radio === null) continue;

            const price = parseFloat(radio.dataset.price);
            const label = radio.dataset.label;

            if (typeof inscriptions[label] === 'undefined') {
                inscriptions[label] = {price: price, quantity: 1};
            } else {
                inscriptions[label].quantity = inscriptions[label].quantity + 1;
            }
            inscriptions[label].subtotal = inscriptions[label].quantity * inscriptions[label].price;
        }

        // Define column headers for display
        const typeLabel = "Type de billet";
        const priceLabel = isSubjectedToVat ? "Prix unitaire HT" : "Prix unitaire";
        const quantityLabel = "Quantité";
        const subtotalLabel = isSubjectedToVat ? "Total HT" : "Total";
        const totalLabel = "Total TTC";

        let numberOfTickets = 0;
        let total = 0;

        // Desktop version (table)
        let desktopHtml = '<table class="summary-table">';
        desktopHtml += '<thead><tr>';
        desktopHtml += `<th>${typeLabel}</th>`;
        desktopHtml += `<th class="text-right">${priceLabel}</th>`;
        desktopHtml += `<th class="text-right">${quantityLabel}</th>`;
        
        if (isSubjectedToVat) {
            desktopHtml += `<th class="text-right">${subtotalLabel}</th>`;
            desktopHtml += `<th class="text-right">${totalLabel}</th>`;
        } else {
            desktopHtml += `<th class="text-right">${subtotalLabel}</th>`;
        }
        
        desktopHtml += '</tr></thead><tbody>';

        // Mobile version (card design)
        let mobileHtml = '<div class="mobile-summary">';

        for (let type in inscriptions) {
            // Desktop row
            desktopHtml += '<tr>';
            desktopHtml += `<td>${type}</td>`;
            
            let priceValue, totalHTValue, totalTTCValue;
            
            if (isSubjectedToVat) {
                priceValue = formatPrice(hasPricesDefinedWithVat ? 
                    computeWithoutTaxesPriceFromPriceWithTaxesConditionally(inscriptions[type].price) : 
                    inscriptions[type].price);
                desktopHtml += `<td class="text-right">${priceValue}€</td>`;
            } else {
                priceValue = formatPrice(inscriptions[type].price);
                desktopHtml += `<td class="text-right">${priceValue}€</td>`;
            }
            
            desktopHtml += `<td class="text-right">x${inscriptions[type].quantity}</td>`;
            
            if (isSubjectedToVat) {
                totalHTValue = formatPrice(hasPricesDefinedWithVat ? 
                    computeWithoutTaxesPriceFromPriceWithTaxesConditionally(inscriptions[type].subtotal) : 
                    inscriptions[type].subtotal);
                desktopHtml += `<td class="text-right">${totalHTValue}€</td>`;
                
                totalTTCValue = formatPrice(hasPricesDefinedWithVat ? 
                    inscriptions[type].subtotal : 
                    computeWithTaxesPriceFromPriceWithoutTaxes(inscriptions[type].subtotal));
                desktopHtml += `<td class="text-right">${totalTTCValue}€</td>`;
            } else {
                totalHTValue = formatPrice(inscriptions[type].subtotal);
                desktopHtml += `<td class="text-right">${totalHTValue}€</td>`;
            }
            
            desktopHtml += '</tr>';
            
            // Mobile card
            mobileHtml += '<div class="mobile-summary-item">';
            mobileHtml += '<div class="mobile-summary-header">';
            mobileHtml += `<div class="mobile-summary-ticket-type">${type}</div>`;
            mobileHtml += `<div class="mobile-summary-quantity">${inscriptions[type].quantity}</div>`;
            mobileHtml += '</div>';
            
            mobileHtml += '<div class="mobile-summary-details">';
            mobileHtml += `<div class="mobile-summary-label">${priceLabel}</div>`;
            mobileHtml += `<div class="mobile-summary-value">${priceValue}€</div>`;
            mobileHtml += '</div>';
            
            if (isSubjectedToVat) {
                mobileHtml += '<div class="mobile-summary-details">';
                mobileHtml += `<div class="mobile-summary-label">${subtotalLabel}</div>`;
                mobileHtml += `<div class="mobile-summary-value">${totalHTValue}€</div>`;
                mobileHtml += '</div>';
                
                mobileHtml += '<div class="mobile-summary-details">';
                mobileHtml += `<div class="mobile-summary-label">${totalLabel}</div>`;
                mobileHtml += `<div class="mobile-summary-value">${totalTTCValue}€</div>`;
                mobileHtml += '</div>';
            } else {
                mobileHtml += '<div class="mobile-summary-details">';
                mobileHtml += `<div class="mobile-summary-label">${subtotalLabel}</div>`;
                mobileHtml += `<div class="mobile-summary-value">${totalHTValue}€</div>`;
                mobileHtml += '</div>';
            }
            
            mobileHtml += '</div>'; // End mobile-summary-item
            
            // Count totals
            numberOfTickets += inscriptions[type].quantity;
            total += hasPricesDefinedWithVat ? inscriptions[type].subtotal : computeWithTaxesPriceFromPriceWithoutTaxes(inscriptions[type].subtotal);
        }

        // Desktop totals row
        desktopHtml += '<tr class="summary-total">';
        desktopHtml += '<td>Total</td><td></td>';
        desktopHtml += `<td class="text-right">x${numberOfTickets}</td>`;
        
        let formattedTotalHT, formattedTotalTTC;
        
        if (isSubjectedToVat) {
            formattedTotalHT = formatPrice(computeWithoutTaxesPriceFromPriceWithTaxesConditionally(total));
            desktopHtml += `<td class="text-right">${formattedTotalHT}€</td>`;
            
            formattedTotalTTC = formatPrice(total);
            desktopHtml += `<td class="text-right">${formattedTotalTTC}€</td>`;
        } else {
            formattedTotalHT = formatPrice(total);
            desktopHtml += `<td class="text-right">${formattedTotalHT}€</td>`;
        }
        
        desktopHtml += '</tr></tbody></table>';

        // Mobile totals
        mobileHtml += '<div class="mobile-summary-total">';
        mobileHtml += `<div class="mobile-summary-total-header">Récapitulatif total</div>`;
        
        mobileHtml += '<div class="mobile-summary-total-row">';
        mobileHtml += `<div class="mobile-summary-label">${quantityLabel}</div>`;
        mobileHtml += `<div class="mobile-summary-value">${numberOfTickets} ${numberOfTickets > 1 ? 'billets' : 'billet'}</div>`;
        mobileHtml += '</div>';
        
        if (isSubjectedToVat) {
            mobileHtml += '<div class="mobile-summary-total-row">';
            mobileHtml += `<div class="mobile-summary-label">${subtotalLabel}</div>`;
            mobileHtml += `<div class="mobile-summary-value">${formattedTotalHT}€</div>`;
            mobileHtml += '</div>';
            
            mobileHtml += '<div class="mobile-summary-total-row">';
            mobileHtml += `<div class="mobile-summary-label">${totalLabel}</div>`;
            mobileHtml += `<div class="mobile-summary-value">${formattedTotalTTC}€</div>`;
            mobileHtml += '</div>';
        } else {
            mobileHtml += '<div class="mobile-summary-total-row">';
            mobileHtml += `<div class="mobile-summary-label">${subtotalLabel}</div>`;
            mobileHtml += `<div class="mobile-summary-value">${formattedTotalHT}€</div>`;
            mobileHtml += '</div>';
        }
        
        mobileHtml += '</div>'; // End mobile-summary-total
        mobileHtml += '</div>'; // End mobile-summary

        // Add both versions to the page
        document.getElementById('ticket-summary').innerHTML = desktopHtml + mobileHtml;
    }


    // Payment options
    const handlePaymentSelection = function() {
        const options = all('.payment-option');
        options.forEach(function(option) {
            option.addEventListener('click', function() {
                option.querySelector('input[type="radio"]').checked = true;
                options.forEach(function(other) {
                    other.classList.toggle('selected', other === option);
                });
            });
        });

        // Alternative payment options toggle
        const toggle = document.getElementById('alt-payment-toggle');
        toggle.addEventListener('click', function() {
            const alternatives = document.querySelector('.payment-alternative-options');
            const icon = toggle.querySelector('i');
            const opening = alternatives.style.display === 'none';

            alternatives.style.display = opening ? '' : 'none';
            icon.classList.toggle('fa-plus-circle', !opening);
            icon.classList.toggle('fa-minus-circle', opening);
        });
    }

    // Auto-fill billing from first ticket
    const setupBillingAutofill = function() {
        ['lastname', 'firstname', 'email'].forEach(function(name) {
            document.getElementById(`purchase_tickets_0_${name}`).addEventListener('change', function() {
                setValue(document.getElementById(`purchase_${name}`), this.value);
            });
        });
    }

    // Transport information
    const cloneTransport = function() {
        const button = document.getElementById('clone_transport');
        if (button === null) {
            return;
        }

        button.style.display = nbInscriptions <= 1 ? 'none' : '';
    }

    const setupCloneTransport = function() {
        const button = document.getElementById('clone_transport');
        if (button === null) {
            return;
        }

        button.addEventListener('click', function(e) {
            e.preventDefault();
            const mode = document.getElementById('purchase_tickets_0_transportMode').value;
            const distance = document.getElementById('purchase_tickets_0_transportDistance').value;

            for (let i = 1; i < nbMaxPersonnes; i++) {
                setValue(document.getElementById(`purchase_tickets_${i}_transportMode`), mode);
                setValue(document.getElementById(`purchase_tickets_${i}_transportDistance`), distance);
            }
        });
    }

    const restoreFromLocalStorage = function() {
        try {
            const storedTickets = localStorage.getItem('tickets');
            if (storedTickets !== null) {
                const tickets = JSON.parse(storedTickets);
                for (const field of form.elements) {
                    // Ne jamais restaurer le token CSRF : sinon on écrase celui,
                    // valide, généré par le serveur pour la session en cours.
                    if (field.name === '' || field.name.endsWith('[_token]') || !Object.hasOwn(tickets, field.name)) continue;
                    const value = tickets[field.name];
                    if (value === '') continue;

                    if (field.type === 'radio' || field.type === 'checkbox') {
                        if (field.value === value) {
                            field.checked = true;
                        }
                    } else {
                        field.value = value;
                    }
                }
            }

            const storedNbPersonnes = localStorage.getItem('nbPersonnes');
            if (storedNbPersonnes !== null) {
                nbInscriptions = parseInt(storedNbPersonnes, 10);
                if (nbPersonnesSelect.querySelector(`option[value="${nbInscriptions}"]`) !== null) {
                    setValue(nbPersonnesSelect, nbInscriptions);
                }
            }
        } catch (e) {
            // There is an error in the value stored, remove it to prevent errors
            localStorage.removeItem('tickets');
            localStorage.removeItem('nbPersonnes');
        }
    }

    const saveToLocalStorage = function() {
        if (!storageAvailable('localStorage')) {
            return;
        }

        const data = {};
        for (const [key, value] of new FormData(form).entries()) {
            // Ne jamais persister le token CSRF en localStorage.
            if (key.endsWith('[_token]')) continue;
            data[key] = value;
        }

        localStorage.setItem('tickets', JSON.stringify(data));
    }

    const handleSubmit = function(e) {
        // Validate final step for payment and billing info
        if (!validateStep(3)) {
            e.preventDefault();
            return;
        }

        // Validate all tickets based on current count
        const currentPersonnes = parseInt(nbPersonnesSelect.value, 10);
        let isValid = true;

        // Validate ticket information only for the selected number of tickets
        for (let i = 0; i < currentPersonnes; i++) {
            // Check if ticket type is selected
            if (checkedTicketType(i) === null) {
                isValid = false;
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
                const field = byName(fieldName);
                if (field.value.trim() === '') {
                    isValid = false;
                    field.focus();
                    alert(`Veuillez remplir tous les champs obligatoires pour la personne ${i+1}.`);
                    break;
                }
            }

            if (!isValid) break;
        }

        if (!isValid) {
            e.preventDefault();
            // Retour à l'étape 2 pour corriger les informations des participants
            goToStep(2);
            return;
        }

        // Check CGV acceptance
        if (!document.getElementById('purchase_cgv').checked) {
            alert('Vous devez accepter les conditions générales de vente.');
            e.preventDefault();
            return;
        }

        // Clear localStorage on successful submission
        if (storageAvailable('localStorage')) {
            localStorage.removeItem('tickets');
            localStorage.removeItem('nbPersonnes');
        }
    }

    // Initialize and set up event listeners
    const init = function() {
        // Ensure all originally required fields are marked with data-required
        markRequiredTicketFields();

        // Initialize steps indicator
        updateStepsIndicator(currentStep);

        // Initial management of ticket sections
        manageTicketSections();

        // Set up navigation buttons
        all('.btn-next').forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                goToStep(currentStep + 1);
            });
        });

        all('.btn-prev').forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                goToStep(currentStep - 1);
            });
        });

        all('.step-item').forEach(function(item) {
            item.addEventListener('click', function(e) {
                if (!item.classList.contains('completed')) return;
                e.preventDefault();
                goToStep(parseInt(item.dataset.step, 10));
            });
        });

        // Set up number of tickets selection
        nbPersonnesSelect.addEventListener('change', function() {
            manageTicketSections();
            cloneTransport();
        });

        // Set up ticket type selection
        form.addEventListener('change', function(e) {
            if (e.target.matches('input[name^="purchase[tickets]"][name$="[ticketEventType]"]')) {
                updateSummary();
            }
        });

        // Set up payment options
        handlePaymentSelection();

        // Set up billing autofill
        setupBillingAutofill();

        setupCloneTransport();

        // Restore from localStorage if available
        if (storageAvailable('localStorage')) {
            restoreFromLocalStorage();
        }

        // Initialize form sections
        manageTicketSections();
        cloneTransport();
        updateSummary();

        // After a server-side validation failure the page re-renders at step 1, hiding field
        // errors that live in step 2 or step 3. Detect those errors and jump to the right step
        // so the user can see what needs to be corrected.
        if (document.querySelector('.tickets--errors p') !== null) {
            if (document.querySelector('#ticket-step-2 .tickets--errors li') !== null) {
                goToStep(2);
            } else if (document.querySelector('#ticket-step-3 .tickets--errors li') !== null) {
                goToStep(3);
            }
        }

        // Handle form submission
        form.addEventListener('submit', handleSubmit);

        // Save form data to localStorage on input change
        form.addEventListener('change', saveToLocalStorage);
    }

    // Start the initialization
    init();
});
