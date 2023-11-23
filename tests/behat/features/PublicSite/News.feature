Feature: Site Public - News

  @reloadDbWithTestData
  Scenario: Accès à la liste des actualités
    Given I am on the homepage
    When I follow "Actualités"
    Then I should see "Actualités"
    And I should see "Les vidéos des talks du Forum PHP 2018 sont disponibles"
    And I follow "Lire l'article: Les vidéos des talks du Forum PHP 2018 sont disponibles"
    Then I should see "Les vidéos des talks du Forum PHP 2018 sont disponibles"
    Then I should see "Cycle de conférences : forum / Année : 2018"

  @reloadDbWithTestData
  Scenario: Filtre sur les actualités
    Given I am on the homepage
    When I follow "Actualités"
    Then I should see "Actualités"
    And I should see "Les vidéos des talks du Forum PHP 2018 sont disponibles"
    And I check "news_filters_year_0"
    And I submit the form with name "news_filters"
    And I should be on "/news/?news_filters[year][0]=2018"
    And I should see "Les vidéos des talks du Forum PHP 2018 sont disponibles"
    And I check "news_filters_theme_0"
    And I submit the form with name "news_filters"
    And I should be on "/news/?news_filters[theme][0]=1"
    And I should not see "Les vidéos des talks du Forum PHP 2018 sont disponibles"
