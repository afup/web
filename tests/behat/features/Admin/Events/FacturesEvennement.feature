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
