Feature: Administration - Healthcheck

  @reloadDbWithTestData
  Scenario: Vérification des dates
    Given I am logged in as admin and on the Administration
    And I follow "Healthcheck"
    Then I should see "Healthcheck"
    And I should see "Pas de différence de timezones"
    And I should not see "Les timezones sont différentes"
