Feature: Administration - Évènements - Vote Visiteur

  @reloadDbWithTestData
  Scenario: Accès aux votes des visiteurs
    Given I am logged in as admin and on the Administration
    And I follow "Votes visiteurs"
    Then the ".content h2" element should contain "Votes des visiteurs"
    And I should see "Aucun vote."