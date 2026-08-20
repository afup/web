Feature: Site Public - Home

  @reloadDbWithTestData
  Scenario: On voit bien toute la page, même le footer
    Given the current date is "2026-01-01"
    And I am on the homepage
    Then I should see "© AFUP 2000-2026"

  @reloadDbWithTestData
  Scenario: Sur la home on a bien un lien vers le dernier article affiché
    Given I am on the homepage

    # Article à la une
    Then I should see "Un article en Markdown"
    And I should see "30 octobre 2025"
    And I should see "Un peu de text avec de la mise en forme"

    # Article dans la liste
    Then I should see "Les vidéos des talks du Forum PHP 2018 sont disponibles"
    And I should see "14/11"
    And I should not see "venez nous voir en octobre au Forum PHP 2019 ou en mai à l'AFUP Day"

    # Affichage de la page de l'article à la une
    When I follow "Lire l'article complet"
    Then I should see "Un article en Markdown"
    And I should see "Lorem ipsum dolor sit amet"

  @reloadDbWithTestData
  Scenario: Affichage des derniers meetups
    Given I am on the homepage
    Then I should see "Le super meetup"
    And I should see "Lyon"
    And I should see "08/11"
