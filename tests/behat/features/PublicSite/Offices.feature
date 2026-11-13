Feature: Site Public - Antennes

  Scenario: La carte des antennes est affichée sans JavaScript
    Given I am on "/association/antennes"
    Then the response status code should be 200
    And I should see "Les antennes"
    And the response should contain "aria-label=\"Carte de France des antennes de l'AFUP\""
    And the response should contain "<a href=\"https://bordeaux.afup.org\" aria-label=\"Bordeaux\">"
    And the response should not contain "mapael"
    And the response should not contain "jquery"
