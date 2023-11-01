Feature: Administration - Planète PHP - Billets

  @reloadDbWithTestData
  Scenario: Gestion des flux
    Given I am logged in as admin and on the Administration
    When I follow "Billets"
    Then the ".content h2" element should contain "Billets"
    And I should see "Un titre Le contenu du super article Actif"
