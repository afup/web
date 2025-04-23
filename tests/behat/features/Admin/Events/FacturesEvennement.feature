Feature: Administration - Évènements - Factures d'évènement

  @reloadDbWithTestData
  Scenario: On peut télécharger le devis
    Given I am logged in as admin and on the Administration
    And I follow "Factures d'évènement"
    Then the ".content h2" element should contain "Factures d'évènement"
    And the ".content table" element should contain "REF-TEST-001"
    When I follow "devis_REF-TEST-001"
    Then the response header "Content-disposition" should match '#attachment; filename="Devis - Michu Bernadette - (.*).pdf"#'

  @reloadDbWithTestData
  @vat
  Scenario: Test d'une facture d'événement avant 2024
    Given I am logged in as admin and on the Administration
    And I follow "Factures d'évènement"
    Then the ".content h2" element should contain "Factures d'évènement"
    And the ".content table" element should contain "REF-TEST-002"
    When I follow "telecharger_REF-TEST-002"
    Then the response header "Content-disposition" should match '#attachment; filename="Facture - Jean Maurice - 2023-06-25_00-00.pdf"#'
    When I parse the pdf downloaded content
    Then The page "1" of the PDF should contain "Objet : Facture n°REF-TEST-002"
    Then The page "1" of the PDF should contain "4 allée des platanes"
    Then The page "1" of the PDF should contain "Type Personne inscrite Prix"
    Then The page "1" of the PDF should contain "2 Jours Maurice Jean 250 €"
    Then The page "1" of the PDF should not contain "Total HT"
    Then The page "1" of the PDF should not contain "Total TVA"
    Then The page "1" of the PDF should contain "TOTAL 250 €"
    Then The page "1" of the PDF should not contain "Total TTC"
    Then The page "1" of the PDF should contain "Payé par CB le 25/06/2023"
    Then The page "1" of the PDF should contain "TVA non applicable - art. 293B du CGI"
    Then The page "1" of the PDF should not contain "TOTAL TTC 250 €"
    Then the checksum of the response content should be "7ee7e977465db7d540cbd4b4d1e31061"

  @reloadDbWithTestData
  @vat
  Scenario: Test d'une facture d'événement après 2024
    Given I am logged in as admin and on the Administration
    And I follow "Factures d'évènement"
    Then the ".content h2" element should contain "Factures d'évènement"
    And the ".content table" element should contain "REF-TEST-003"
    When I follow "telecharger_REF-TEST-003"
    Then the response header "Content-disposition" should match '#attachment; filename="Facture - Kirk James Tiberius - 2024-01-02_00-00.pdf"#'
    When I parse the pdf downloaded content
    Then The page "1" of the PDF should contain "Objet : Facture n°REF-TEST-003"
    Then The page "1" of the PDF should contain "3 avenue de l'enterprise"
    Then The page "1" of the PDF should contain "Type Personne inscrite Prix HT TVA Prix TTC"
    Then The page "1" of the PDF should contain "2 Jours James Tiberius Kirk 227,27 € 10% 250,00 €"
    Then The page "1" of the PDF should contain "Total HT 227,27 €"
    Then The page "1" of the PDF should contain "Total TVA 10% 22,73 €"
    Then The page "1" of the PDF should contain "TOTAL TTC 250,00 €"
    Then The page "1" of the PDF should contain "Payé par CB le 02/01/2024"
    Then The page "1" of the PDF should not contain "TVA non applicable - art. 293B du CGI"
    Then The page "1" of the PDF should contain "Numéro de TVA intracommunautaire FR27 500 869 011"
    Then the checksum of the response content should be "50c90902d9702d79a93550fa8b93f3fd"

  @reloadDbWithTestData
  @clearEmails
  Scenario: On passe à l'état facturée
    Given I am logged in as admin and on the Administration
    And I follow "Factures d'évènement"
    When I follow "facture_REF-TEST-001"
    Then the ".content .message" element should contain "La facture est prise en compte"
    # envoi par email de la facture
    When I follow "email_REF-TEST-001"
    And I should only receive the following emails:
      | from               | to                                         | subject                |
      | <bonjour@afup.org> | <bernadette@yahoo.fr>,<tresorier@afup.org> | Facture évènement AFUP |
    Then the ".content .message" element should contain "La facture a été envoyée"

  @reloadDbWithTestData
  Scenario: On peut télécharger la facture
    Given I am logged in as admin and on the Administration
    And I follow "Factures d'évènement"
    When I follow "facture_REF-TEST-001"
    And I follow "telecharger_REF-TEST-001"
    Then the response header "Content-disposition" should match '#attachment; filename="Facture - Michu Bernadette - (.*).pdf"#'

  @reloadDbWithTestData
  Scenario: On peut supprimer la facture
    Given I am logged in as admin and on the Administration
    And I follow "Factures d'évènement"
    And I follow "facture_REF-TEST-001"
    When I follow "supprimer_REF-TEST-001"
    Then the ".content .message" element should contain "La facture est supprimée"
