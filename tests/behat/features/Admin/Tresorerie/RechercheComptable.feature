Feature: Administration - Trésorerie - Recherche comptable

  @reloadDbWithTestData
  Scenario: Recherche Comptable pour raoul
    Given I am logged in as admin and on the Administration
    When I follow "Recherche comptable"
    Then the ".content h2" element should contain "Recherche comptable"
    When I fill in "q" with "raoul"
    And I press "Rechercher"
    And I should see "Jul 13, 2018 > Jul 8, 2019 150.00 Dupont Raoul <raoul.dupont@mycorp.fr>"
