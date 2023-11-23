Feature: Planete PHP - Home

  @reloadDbWithTestData
  Scenario: On voit bien toute la page
    Given I am on the homepage
    Then I should see "planete-php.fr"
    Then I should see "Un titre"
    Then I should see "Un 2e titre"
    Then I should see "Envoyez un email avec l'URL du site et du flux à planetephpfr AT afup POINT org"
    When I follow "Articles précédents"
    Then I should see "Articles précédents Articles suivants"
