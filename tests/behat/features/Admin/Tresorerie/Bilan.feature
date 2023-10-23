Feature: Administration - Trésorerie - Bilan

  @reloadDbWithTestData
  Scenario: Bilan afficher
    Given I am logged in as admin and on the Administration
    When I follow "Bilan"
    Then the ".content h2" element should contain "Bilan"
    And I should see "AG 500,00"
    And I should see "AG 1 000,00"
    And I follow the button of tooltip "Déplier les écritures de débit/crédit pour AG"
    And I should see "18/10/2023 Une dépense très utile 500,00"
    And I should see "17/10/2023 Une recette qui rapporte 1 000,00"
