Feature: Administration - Évènements - Vote Visiteur

  @reloadDbWithTestData

  Scenario: Un membre ne peut pas accéder aux votes visiteurs
    Given I am logged-in with the user "paul" and the password "paul"
    And I am on "/admin/event/votes/"
    Then the response status code should be 403

  Scenario: Accès aux votes des visiteurs
    Given I am logged in as admin and on the Administration
    And I follow "Votes visiteurs"
    Then the ".content h2" element should contain "Votes des visiteurs"
    And I should see "Aucun vote."
