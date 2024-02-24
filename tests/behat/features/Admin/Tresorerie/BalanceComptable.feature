Feature: Administration - Trésorerie - Balance comptable

  @reloadDbWithTestData
  Scenario: Balance comptable afficher
    Given I am logged in as admin and on the Administration
    When I follow "Balance comptable"
    Then the ".content h2" element should contain "Balance comptable"
    And I should see "AG 500,00 1 000,00"
    And I follow the button of tooltip "Voir sur la page courante"
    And I should see "Assurances Une recette qui rapporte 0,00 1 000,00"
    And I should see "Assurances Une dépense très utile 500,00 0,00"
