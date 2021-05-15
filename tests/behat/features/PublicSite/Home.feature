Feature: Site Public - Home

  @reloadDbWithTestData
  Scenario: On voit bien toute la page, même le footer
    Given I am on the homepage
    Then I should see "© AFUP 2003-"

  @reloadDbWithTestData
  Scenario: Sur la home a a bien la partie "Talk du jour d'affichée"
    Given I am on the homepage
    Then I should see "Le talk du jour"

  @reloadDbWithTestData
  Scenario: Sur la home a a bien un lien vers le dernier article affiché
    Given I am on the homepage
    Then I should see "Les vidéos des talks du Forum PHP 2018 sont disponibles"
    When I follow "14/11"
    Then I should see "14 novembre 2018"
    And I should see "venez nous voir en octobre au Forum PHP 2019 ou en mai à l'AFUP Day"
