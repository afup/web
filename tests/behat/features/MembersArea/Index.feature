Feature: Espace membre, accueil

  @reloadDbWithTestData
  Scenario: L'accueil affiche le nom et prénom du compte
    Given I am logged in as admin
    And I follow "Espace membre"
    Then I should see "Espace membre"
    And I should see "Mes coordonnées : Admin Admin"
