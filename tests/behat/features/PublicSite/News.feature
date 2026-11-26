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

  @reloadDbWithTestData
  Scenario: Les aperçus des actualités sont en texte brut
    Given I am on "/news/"
    Then I should see "Un peu de text avec de la mise en forme"
    And I should not see "**avec de la mise**"
    And I should see "Comme à chaque édition du Forum PHP"
    When I follow "Lire l'article: Un article en Markdown"
    Then the response should contain "<meta property=\"og:description\" content=\"Un peu de text avec de la mise en forme\" />"
    And the response should contain "<meta name=\"twitter:description\" content=\"Un peu de text avec de la mise en forme\">"

  # Données de test : 14 actualités en 2017, dont 7 du thème « Cycles de conférences » (valeur 1).
  @reloadDbWithTestData
  Scenario: La pagination d'une liste filtrée par thème garde le filtre
    Given I am on "/news/?news_filters[theme][0]=1"
    Then the response status code should be 200
    And the response should contain "href=\"/news/?page=2&amp;news_filters%5Btheme%5D%5B0%5D=1\""
    And the response should not contain "%5Bname%5D"
    And the response should not contain "%5Bvalue%5D"
    When I am on "/news/?page=2&news_filters[theme][0]=1"
    Then the response status code should be 200
    And the "news_filters_theme_0" checkbox should be checked
    # 7 actualités du thème, 5 par page
    And I should see 2 "article" elements

  @reloadDbWithTestData
  Scenario: La pagination d'une liste filtrée par année garde le filtre
    Given I am on "/news/?news_filters[year][0]=2017"
    Then the response should contain "href=\"/news/?page=2&amp;news_filters%5Byear%5D%5B0%5D=2017\""
    When I am on "/news/?page=3&news_filters[year][0]=2017"
    Then the response status code should be 200
    And the "news_filters_year_2" checkbox should be checked
    And I should see 4 "article" elements

  @reloadDbWithTestData
  Scenario: Une liste filtrée n'est pas indexée, la liste non filtrée l'est
    Given I am on "/news/?news_filters[year][0]=2017"
    Then the response should contain "<meta name=\"robots\" content=\"noindex, follow\">"
    When I am on "/news/"
    Then the response should not contain "<meta name=\"robots\""
    And the response should contain "<link rel=\"canonical\" href=\"https://apachephptest:80/news/\" />"
    When I am on "/news/?page=2"
    Then the response should not contain "<meta name=\"robots\""
    And the response should contain "<link rel=\"canonical\" href=\"https://apachephptest:80/news/?page=2\" />"

  @reloadDbWithTestData
  Scenario: La canonical d'une liste filtrée est la page filtrée elle-même
    Given I am on "/news/?page=2&news_filters[year][0]=2017"
    Then the response should contain "<link rel=\"canonical\" href=\"https://apachephptest:80/news/?page=2&amp;news_filters%5Byear%5D%5B0%5D=2017\" />"
    When I am on "/news/?news_filters[year][0]=2017"
    Then the response should contain "<link rel=\"canonical\" href=\"https://apachephptest:80/news/?news_filters%5Byear%5D%5B0%5D=2017\" />"

  @reloadDbWithTestData
  Scenario Outline: La canonical d'une liste filtrée ne dépend pas de l'ordre des paramètres
    Given I am on "<url>"
    Then the response status code should be 200
    And the response should contain "<link rel=\"canonical\" href=\"https://apachephptest:80/news/?news_filters%5Byear%5D%5B0%5D=2017&amp;news_filters%5Byear%5D%5B1%5D=2018&amp;news_filters%5Btheme%5D%5B0%5D=1\" />"

    Examples:
      | url                                                                                  |
      | /news/?news_filters[year][0]=2017&news_filters[year][1]=2018&news_filters[theme][0]=1 |
      | /news/?news_filters[year][0]=2018&news_filters[year][1]=2017&news_filters[theme][0]=1 |
      | /news/?news_filters[theme][0]=1&news_filters[year][0]=2018&news_filters[year][1]=2017 |

  @reloadDbWithTestData
  Scenario Outline: Un filtre inconnu ou sans résultat répond 404
    Given I am on "<url>"
    Then the response status code should be 404

    Examples:
      | url                                                     |
      | /news/?news_filters[year][0]=1990                       |
      | /news/?news_filters[theme][0]=99                        |
      | /news/?news_filters[event][0]=999                       |
      | /news/?news_filters[year][0]=2018&news_filters[theme][0]=1 |
      | /news/?page=4&news_filters[theme][0]=1                  |

  @reloadDbWithTestData
  Scenario: Titre et meta description de la liste des actualités
    Given I am on "/news/"
    Then I should see 1 "h1" elements
    And the "h1" element should contain "Actualités de l'AFUP"
    And the page should have a meta description

  @reloadDbWithTestData
  Scenario: Meta description d'un article tirée de son chapeau, sans Markdown
    Given I am on "/news/"
    When I follow "Lire l'article: Un article en Markdown"
    Then the page should have a meta description
    And the meta description should be "Un peu de text avec de la mise en forme"
