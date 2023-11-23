Feature: Administration - Logs

  @reloadDbWithTestData
  Scenario: Afficher les logs
    Given I am logged in as admin and on the Administration
    And I follow "Logs"
    Then I should see "Logs"
    And I should see "Date Nom Prénom Texte"
