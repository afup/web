Feature: Administration - Trésorerie - Devis/Facture

  @reloadDbWithTestData
  @clearEmails
  @javascript
  Scenario: Créer/Modifier un devis, une facture
    Given I am logged in as admin and on the Administration
    When I open menu "Trésorerie"
    When I follow "Devis"
    Then the ".content h2" element should contain "Liste des devis"
    And I follow "Ajouter"
    Then I should see "Ajouter un devis"
    # Création du devis incomplet
    When I fill in "quotation[company]" with "ESN Corp"
    And I fill in "quotation[address]" with "3 rue du chemin"
    And I fill in "quotation[city]" with "Dijon"
    And I fill in "quotation[zipcode]" with "21000"
    And I fill in "quotation[email]" with "martine@ens-corp.biz"
    When I press "Ajouter" and wait until I see "L'écriture a été ajoutée"
    And I should see "ESN Corp"
    And I should see "0,00"
    And I should see tooltip "Modifier le devis ESN Corp"
    And I should see tooltip "Télécharger le devis ESN Corp"
    And I should see tooltip "Transférer la ligne ESN Corp en facture"
    # Création du devis complet
    When I follow "Ajouter"
    Then I should see "Ajouter un devis"
    When I fill in "quotation[company]" with "ESN dev en folie"
    And I fill in "quotation[service]" with "Développement"
    And I fill in "quotation[address]" with "1 rue du chemin"
    And I fill in "quotation[zipcode]" with "75000"
    And I fill in "quotation[city]" with "Paris"
    And I fill in "quotation[lastname]" with "Moreau"
    And I fill in "quotation[firstname]" with "Martine"
    And I fill in "quotation[phone]" with "0101010101"
    And I fill in "quotation[email]" with "martine@ens-en-folie.biz"
    And I fill in "quotation[tvaIntra]" with "FR7612345"
    And I fill in "quotation[refClt1]" with "CLIENT-AFGD5S"
    And I fill in "quotation[refClt2]" with "AFGD5S"
    And I fill in "quotation[refClt3]" with "000AFGD5S"
    And I fill in "quotation[observation]" with "Ce devis ne comprend pas les selfies avec l'équipe"
    Then I click on link with class "add_item_link"
    And I fill in "quotation[details][0][reference]" with "COACH-001"
    And I fill in "quotation[details][0][designation]" with "Coaching d'équipe pour l'accompagnement sans douleur à Symfony"
    And I fill in "quotation[details][0][quantity]" with "12"
    And I fill in "quotation[details][0][unitPrice]" with "660"
    Then I click on link with class "add_item_link"
    And I fill in "quotation[details][1][reference]" with "ARCHI-007"
    And I fill in "quotation[details][1][designation]" with "Architecture en KKK"
    And I fill in "quotation[details][1][quantity]" with "1"
    And I fill in "quotation[details][1][unitPrice]" with "12000"
    When I press "Ajouter" and wait until I see "L'écriture a été ajoutée"
    Then I should see "L'écriture a été ajoutée"
    And I should see "ESN dev en folie"
    And I should see "Paris"
    And I should see "CLIENT-AFGD5S"
    And I should see "19 920,00"
    And I should see a red label "non"
    And I should see tooltip "Modifier le devis ESN dev en folie"
    And I should see tooltip "Télécharger le devis ESN dev en folie"
    And I should see tooltip "Transférer la ligne ESN dev en folie en facture"
    # Modification du devis
    When I follow the button of tooltip "Modifier le devis ESN dev en folie"
    And I fill in "quotation[city]" with "Paris Cedex 1"
    When I press "Modifier" and wait until I see "L'écriture a été modifiée"
    Then I should see "L'écriture a été modifiée"
    And I should see "ESN dev en folie"
    And I should see "Paris Cedex 1"
    And I should see "CLIENT-AFGD5S"
    And I should see "19 920,00"
    # Suppression d'une ligne dans un devis
    When I follow the button of tooltip "Modifier le devis ESN dev en folie"
    Then I click on link with id "remove_row_0"
    When I press "Modifier" and wait until I see "L'écriture a été modifiée"
    Then I should see "L'écriture a été modifiée"
    And I should see "ESN dev en folie"
    And I should see "Paris Cedex 1"
    And I should see "CLIENT-AFGD5S"
    And I should see "12 000,00"
    # Transformation du devis en facture
    When I go to "/admin/"
    When I open menu "Trésorerie"
    And I follow "Devis"
    Then I follow the button of tooltip "Transférer la ligne ESN dev en folie en facture"
    And I should see "Le devis a été transformé en facture"
    And I should see "Liste des factures"
    And I should see "Il n'est pas possible de créer directement une facture"
    And I should see "ESN dev en folie"
    And I should see "Paris Cedex 1"
    And I should see a yellow label "En attente"
    # Modification de la facture
    Then I follow the button of tooltip "Modifier la ligne ESN dev en folie"
    And I should see "Modifier une facture"
    Then I fill in "ville" with "Paris Cedex 7"
    Then I select "1" from "etat_paiement"
    When I press "Modifier" and wait until I see "L'écriture a été modifiée"
    And I should see "Paris Cedex 7"
    And I should see "Payé"
    # Envoi de la facture par email
    Then I follow the button of tooltip "Envoyer la facture 2026-3 par mail"
    And I should only receive the following emails:
      | from               | to                         | subject      |
      | <bonjour@afup.org> | <martine@ens-en-folie.biz> | Facture AFUP |
    Then the ".content .message" element should contain "La facture a été envoyée"
    # Lien de paiement
    Then I follow the button of tooltip "Récupérer le lien de paiement en ligne"
    Then I should see "Paiement en ligne de la facture"
    Then I should see "Télécharger la facture en PDF"

  # On n'utilise pas @reloadDbWithTestData pour conserver les données
  Scenario: Téléchargement d'un devis et d'une facture
    Given I am logged in as admin and on the Administration
    When I follow "Devis"
    # Téléchargement du devis
    And I follow the button of tooltip "Télécharger le devis ESN dev en folie"
    Then the response header "Content-disposition" should match '#attachment; filename="Devis - ESN dev en folie - (.*).pdf"#'
    When I parse the pdf downloaded content
    Then The page "1" of the PDF should contain "ESN dev en folie"
    # Téléchargement de la facture
    When I go to "/admin/"
    And I follow "Factures"
    And I follow the button of tooltip "Télécharger la facture 2026-3"
    Then the response header "Content-disposition" should match '#attachment; filename="Facture - ESN dev en folie - (.*).pdf"#'
    When I parse the pdf downloaded content
    Then The page "1" of the PDF should contain "N° TVA Intracommunautaire : FR7612345"

  @reloadDbWithTestData
  @clearEmails
  Scenario: Vérification de la devise par défaut (Euro)
    # Téléchargement du devis
    Given I am logged in as admin and on the Administration
    When I go to "/admin/accounting/quotations/list?periodId=16"
    Then the ".content h2" element should contain "Liste des devis"
    And I should see "My company Ltd"
    And I follow the button of tooltip "Télécharger le devis My company Ltd"
    Then the response header "Content-disposition" should match '#attachment; filename="Devis - My company Ltd - (.*).pdf"#'
    When I parse the pdf downloaded content
    Then The page "1" of the PDF should contain "0,00 €"

  @reloadDbWithTestData
  @clearEmails
  @vat
  Scenario: Test du PDF de facture avant 2024
    Given I am logged in as admin and on the Administration
    When I go to "/admin/accounting/invoices/list?periodId=14"
    Then the ".content h2" element should contain "Factures"
    And I should see "Il n'est pas possible de créer directement une facture"
    When I follow the button of tooltip "Télécharger la facture 2023-01"
    Then the response header "Content-disposition" should equal 'attachment; filename="Facture - Krampouz - 2023-06-11.pdf"'
    Given I parse the pdf downloaded content
    Then The page "1" of the PDF should contain "Le 11/06/2023"
    Then The page "1" of the PDF should contain "Krampouz"
    Then The page "1" of the PDF should contain "3, rue du port"
    Then The page "1" of the PDF should contain "Facture n° 2023-01"
    Then The page "1" of the PDF should contain "Repère(s) :  Forum PHP 2023"
    Then The page "1" of the PDF should contain "Comme convenu, nous vous prions de trouver votre facture"
    Then The page "1" of the PDF should contain "Type Description Quantite Prix Total"
    Then The page "1" of the PDF should contain "forum_php_2023 Forum PHP 2023 - Sponsoring Bronze 1.00 1000.00 € 1000 €"
    Then The page "1" of the PDF should contain "TOTAL 1000 €"
    Then The page "1" of the PDF should contain "TVA non applicable - art. 293B du CGI"

  @reloadDbWithTestData
  @clearEmails
  @vat
  Scenario: Test du PDF de facture après 2024
    Given I am logged in as admin and on the Administration
    When I go to "/admin/accounting/invoices/list?periodId=16"
    Then the ".content h2" element should contain "Factures"
    And I should see "Il n'est pas possible de créer directement une facture"
    When I follow the button of tooltip "Télécharger la facture 2025-02"
    Then the response header "Content-disposition" should equal 'attachment; filename="Facture - Krampouz - 2025-01-04.pdf"'
    Given I parse the pdf downloaded content
    Then The page "1" of the PDF should contain "Le 04/01/2025"
    Then The page "1" of the PDF should contain "Krampouz"
    Then The page "1" of the PDF should contain "3, rue du port"
    Then The page "1" of the PDF should contain "Facture n° 2025-02"
    Then The page "1" of the PDF should contain "Repère(s) :  Forum PHP 2025"
    Then The page "1" of the PDF should contain "Comme convenu, nous vous prions de trouver votre facture"
    Then The page "1" of the PDF should contain "Type Description Quantite TVA Prix HT Total TTC"
    Then The page "1" of the PDF should contain "forum_php_2025 Forum  PHP  2025  -  Sponsoring"
    Then The page "1" of the PDF should contain "Bronze 1.00 20.00% 1 000,00 € 1 200,00 €"
    Then The page "1" of the PDF should contain "TOTAL HT 1 000,00 €"
    Then The page "1" of the PDF should contain "Total TVA 20.00% 200,00 €"
    Then The page "1" of the PDF should contain "TOTAL TTC 1 200,00 €"
    Then The page "1" of the PDF should not contain "TVA non applicable - art. 293B du CGI"

  @reloadDbWithTestData
  @vat
  Scenario: Test du PDF de devis avant 2024
    Given I am logged in as admin and on the Administration
    When I go to "/admin/accounting/quotations/list?periodId=14"
    Then the ".content h2" element should contain "Liste des devis"
    When I follow the button of tooltip "Télécharger le devis Krampouz"
    Then the response header "Content-disposition" should equal 'attachment; filename="Devis - Krampouz - 2023-06-10.pdf"'
    Given I parse the pdf downloaded content
    Then The page "1" of the PDF should contain "Le 10/06/2023"
    Then The page "1" of the PDF should contain "Krampouz"
    Then The page "1" of the PDF should contain "3, rue du port"
    Then The page "1" of the PDF should contain "Devis n° 2023-01"
    Then The page "1" of the PDF should contain "Repère(s) :  Forum PHP 2023"
    Then The page "1" of the PDF should contain "Comme convenu, nous vous prions de trouver votre devis"
    Then The page "1" of the PDF should contain "Type Description Quantite Prix Total"
    Then The page "1" of the PDF should contain "forum_php_2023 Forum PHP 2023 - Sponsoring Bronze 1.00 1000.00 € 1000 €"
    Then The page "1" of the PDF should contain "TOTAL 1000 €"
    Then The page "1" of the PDF should contain "TVA non applicable - art. 293B du CGI"
    Then the checksum of the response content should be "c424b17649ae6bfc83bf6791dc1543ab"


  @reloadDbWithTestData
  @vat
  Scenario: Test du PDF de devis après 2024
    Given I am logged in as admin and on the Administration
    When I go to "/admin/accounting/quotations/list?periodId=16"
    Then the ".content h2" element should contain "Liste des devis"
    When I follow the button of tooltip "Télécharger le devis Krampouz"
    Then the response header "Content-disposition" should equal 'attachment; filename="Devis - Krampouz - 2025-01-03.pdf"'
    Given I parse the pdf downloaded content
    Then The page "1" of the PDF should contain "Le 03/01/2025"
    Then The page "1" of the PDF should contain "Krampouz"
    Then The page "1" of the PDF should contain "3, rue du port"
    Then The page "1" of the PDF should contain "Devis n° 2025-02"
    Then The page "1" of the PDF should contain "Repère(s) :  Forum PHP 2025"
    Then The page "1" of the PDF should contain "Comme convenu, nous vous prions de trouver votre devis"
    Then The page "1" of the PDF should contain "Type Description Quantite TVA Prix HT Total TTC"
    Then The page "1" of the PDF should contain "forum_php_2025 Forum  PHP  2025  -  Sponsoring"
    Then The page "1" of the PDF should contain "Bronze 1.00 20.00% 1 000,00 € 1 200,00 €"
    Then The page "1" of the PDF should contain "TOTAL HT 1 000,00 €"
    Then The page "1" of the PDF should contain "Total TVA 20.00% 200,00 €"
    Then The page "1" of the PDF should contain "TOTAL TTC 1 200,00 €"
    Then The page "1" of the PDF should not contain "TVA non applicable - art. 293B du CGI"
