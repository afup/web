Feature: Page de connexion

  @reloadDbWithTestData
  Scenario: Aucune connexion
    Given I am logged in as admin and on the Administration
    When I follow "Personnes physiques"
    And I follow the button of tooltip "Modifier la fiche de Personne Paul"
    Then I should see "Aucune connexion"

  @reloadDbWithTestData
  Scenario: Date de dernière connexion enregistrée
    Given the current date is "2026-03-10 13:56:28"
    And I am logged-in with the user paul and the password paul
    And I follow "Se déconnecter"
    When the current date is "2026-03-20 09:12:34"
    And I am logged in as admin and on the Administration
    And I follow "Personnes physiques"
    And I follow the button of tooltip "Modifier la fiche de Personne Paul"
    Then I should see "Dernière connexion : 10/03/2026 13:56:28"
