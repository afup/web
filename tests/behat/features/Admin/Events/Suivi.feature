Feature: Administration - Évènements - Suivi

  Scenario: Un membre ne peut pas accéder aux stats
    Given I am logged-in with the user "paul" and the password "paul"
    And I am on "/admin/event/stats"
    Then the response status code should be 403

  @reloadDbWithTestData
  Scenario: On vérifie la page du suivi
    Given I am logged in as admin and on the Administration
    And I follow "Suivi"
    Then the ".content h2" element should contain "Suivi"
    And the response should contain "Évolution des inscriptions"
    And the response should contain "Jours de vente restants"
    And the response should contain "142 jours restants"
    And the response should contain "Progression des ventes: Jour 1"
    And the response should contain "3 places vendues sur"
    And the response should contain "Progression des ventes: Jour 2"
    And the response should contain "3 places vendues sur"
    And the response should contain "Répartition des billets"
