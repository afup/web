Feature: Administration - Planète PHP - Billets

  Scenario: Un membre ne peut pas accéder à la gestion de planète PHP
    Given I am logged-in with the user "paul" and the password "paul"
    And I am on "/admin/planete/feed-articles"
    Then the response status code should be 403

  @reloadDbWithTestData
  Scenario: Gestion des flux
    Given I am logged in as admin and on the Administration
    When I follow "Billets"
    Then the ".content h2" element should contain "Billets"
    And I should see "Un titre Le contenu du super article Actif"
