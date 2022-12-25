Feature: Administration - Relance des cotisations

  @reloadDbWithTestData
  Scenario: On test la liste des relances des cotisations
    Given I am logged in as admin and on the Administration
    And I follow "Relances cotisations"
    Then I should see "Relance des cotisations"
    And I should see "Personne Paul" in the "tbody > tr:first-child > td:nth-child(1)" element
    And I should see "Ok" in the "tbody > tr:first-child > td:nth-child(3)" element
    And I should see "15DaysAfter" in the "tbody > tr:first-child > td:nth-child(4)" element
    And I should see "01/04/2022" in the "tbody > tr:first-child > td:nth-child(5)" element
    And I should see "Edmond Dupont" in the "tbody > tr:nth-child(3) > td:nth-child(1)" element
    And I should see "MyCorp" in the "tbody > tr:nth-child(3) > td:nth-child(2)" element
    And I should see "Ok" in the "tbody > tr:nth-child(3) > td:nth-child(3)" element
    And I should see "15DaysAfter" in the "tbody > tr:nth-child(3) > td:nth-child(4)" element
    And I should see "01/02/2022" in the "tbody > tr:nth-child(3) > td:nth-child(5)" element
