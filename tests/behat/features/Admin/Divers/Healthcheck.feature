Feature: Administration - Healthcheck

  @reloadDbWithTestData
  Scenario: Un membre ne peut pas accéder aux vérifications du site
    Given I am logged-in with the user "paul" and the password "paul"
    And I am on "/admin/healthcheck"
    Then the response status code should be 403

  @reloadDbWithTestData
  Scenario: Vérifications du site
    Given I am logged in as admin and on the Administration
    And I follow "Healthcheck"
    Then I should see "Healthcheck"
    And I should see "Pas de différence de timezones"
    And I should not see "Les timezones sont différentes"
