Feature: Site Public - News

  @reloadDbWithTestData
  Scenario: Accès à la liste des actualités
    Given I am on the homepage
    When I follow "Actualités"
    Then I should see "Actualités"
    And I should see "Les vidéos des talks du Forum PHP 2018 sont disponibles"
    And I follow "Lire l'article: Les vidéos des talks du Forum PHP 2018 sont disponibles"
    Then I should see "Les vidéos des talks du Forum PHP 2018 sont disponibles"
    Then I should see "forum"
    Then I should see "2018"

  @reloadDbWithTestData
  Scenario: Filtre sur les actualités
    Given I am on the homepage
    When I follow "Actualités"
    Then I should see "Actualités"
    And I should see "Les vidéos des talks du Forum PHP 2018 sont disponibles"
    And I check "news_filters_year_1"
    And I submit the form with name "news_filters"
    And I should be on "/news/?news_filters[year][0]=2018"
    And I should see "Les vidéos des talks du Forum PHP 2018 sont disponibles"
    And I check "news_filters_theme_0"
    And I submit the form with name "news_filters"
    And I should be on "/news/?news_filters[theme][0]=1"
    And I should not see "Les vidéos des talks du Forum PHP 2018 sont disponibles"

  @reloadDbWithTestData
  Scenario: Pagination de la liste des actualités
    # 16 actualités, 5 par page : la dernière page, incomplète, est liée
    Given I am on "/news/"
    Then the response should contain "href=\"/news/?page=4\""
    And the response should contain "aria-current=\"page\""
    When I am on "/news/?page=4"
    Then the response status code should be 200
    And the response should contain "<link rel=\"canonical\" href=\"https://apachephptest:80/news/?page=4\" />"
    And the response should not contain "rel=\"next\""
    When I am on "/news/?page=5"
    Then the response status code should be 404
    When I am on "/news/?page=0"
    Then the response status code should be 404

  @reloadDbWithTestData
  Scenario: Affichage d'un article MARKDOWN
    Given I am on the homepage
    When I follow "Actualités"
    And I follow "Lire l'article: Un article en Markdown"
    Then I should not see "### Un premier titre !"
    And the response should contain the html
    """
    <h3 id="content-un-premier-titre">Un premier titre !</h3>
    """
    And I should not see "**de texte en gras**"
    And the response should contain the html "<strong>de texte en gras</strong>"
    And I should not see "*de texte en italic*"
    And the response should contain the html "<em>de texte en italic</em>"
    And I should not see "[commodi delectus](https://afup.org)"
    And the response should contain the html
    """
    <a href="https://afup.org">commodi delectus</a>
    """
