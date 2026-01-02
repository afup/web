Feature: Administration - Trésorerie - Compte banques

  @reloadDbWithTestData
  Scenario: Compte banques voir le journal de banque
    Given I am logged in as admin and on the Administration
    When I follow "Compte banques"
    Then the ".content h2" element should contain "Journal de banque"
    Then I should see "Carte Bleue Une recette qui rapporte 1 000,00"
    Then I should see "Carte Bleue Une dépense très utile 500,00"
    Then I should see "Sous Total 500,00 1 000,00 500,00"
    Then I should see "Carte Bleue Une dépense très utile 500,00"
    Then I should see "Sous Total 100,00 0,00 400,00"
    Then I should see "600,00 Total débit"
    Then I should see "1 000,00 Total crédit"
    Then I should see "400,00 solde"

  @reloadDbWithTestData
  Scenario: Compte banques Export Excel
    Given I am logged in as admin and on the Administration
    When I follow "Compte banques"
    And I follow "Export XLSX"
    Then the response header "Content-Disposition" should match '#filename="compta_afup_(.*).xlsx"#'
