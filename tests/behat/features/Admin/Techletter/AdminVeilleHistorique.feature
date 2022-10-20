Feature: Administration - Veille - Historique

  @reloadDbWithTestData @test
  Scenario: Accès à l'historique de la veille
    Given I am logged in as admin and on the Administration
    And I follow "Historique"
    Then the ".content h2" element should contain "Historique"
    And I should see 1 "thead > tr" elements
    And I should see 16 "tbody > tr" elements
    And I should see 9 "tbody > tr > td:contains('04/04/2018')" elements
    And I should see 7 "tbody > tr > td:contains('18/04/2018')" elements
    And I should see 9 "tbody > tr > td:contains('article')" elements
    And I should see 7 "tbody > tr > td:contains('project')" elements
    And I should see "04/04/2018" in the "tbody > tr:first-child > td:nth-child(1)" element
    And I should see "article" in the "tbody > tr:first-child > td:nth-child(2)" element
    And I should see "https://kevinsmith.io/modern-php-without-a-framework" in the "tbody > tr:first-child > td:nth-child(3) > a" element
    And I should see "Modern PHP Without a Framework | Kevin Smith" in the "tbody > tr:first-child > td:nth-child(4)" element
    And I should see "18/04/2018" in the "tbody > tr:last-child > td:nth-child(1)" element
    And I should see "project" in the "tbody > tr:last-child > td:nth-child(2)" element
    And I should see "https://github.com/tpunt/phactor" in the "tbody > tr:last-child > td:nth-child(3) > a" element
    And I should see "tpunt/phactor" in the "tbody > tr:last-child > td:nth-child(4)" element
