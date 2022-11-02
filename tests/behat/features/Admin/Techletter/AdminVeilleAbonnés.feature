Feature: Administration - Veille - Abonnés

  @reloadDbWithTestData @test
  Scenario: Accès aux abonnés de la veille technique
    Given I am logged in as admin and on the Administration
    And I follow "Abonnés"
    Then the ".content .statistic .value" element should contain "1"
    And the ".content .statistic .label" element should contain "abonnés"
    And I should see "Nom" in the "thead > tr:first-child > th:nth-child(1)" element
    And I should see "Date d'enregistrement" in the "thead > tr:first-child > th:nth-child(2)" element
    And I should see "Admin Admin" in the "tbody > tr:first-child > td:nth-child(1)" element