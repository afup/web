Feature: Administration - Partie Reporting

  @reloadDbWithTestData
  Scenario: Accès au reporting
    Given I am logged in as admin and on the Administration
    And I follow "Reporting"
    Then the ".content h2" element should contain "Statistiques concernant les membres"
    Then I should see "1 Personnes physiques totales*"
    Then I should see "1 Personnes physiques non rattachées à une personne morale*"
