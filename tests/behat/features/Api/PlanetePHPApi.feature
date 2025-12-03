Feature: PlanetePHP API routes

  Scenario: Get the first page of articles
    Given I am on "/planete-php-api/articles"
    Then the response status code should be 200
    And the response should be in json
    And the response header "X-Pagination-Total" should equal 26
    And the response header "X-Pagination-Per-Page" should equal 20
    And the response header "X-Pagination-Has-Next-Page" should equal true
    And the json response has the key "$[0].title" with value "Un titre"
    And the json response has the key "$[0].url" with value "https://afup.org/url.html"
    And the json response has the key "$[0].author" with value "Un super auteur"
    And the json response has the key "$[0].content" with value "Le contenu du super article"

  Scenario: Get another page of articles
    Given I am on "/planete-php-api/articles?page=2"
    Then the response status code should be 200
    And the response should be in json
    And the response header "X-Pagination-Total" should equal 26
    And the response header "X-Pagination-Per-Page" should equal 20
    And the response header "X-Pagination-Has-Next-Page" should equal false
    And the json response has the key "$[0].title" with value "Un titre 18"
    And the json response has the key "$[0].url" with value "https://afup.org/url-18.html"
    And the json response has the key "$[0].author" with value "Un super auteur 18"
    And the json response has the key "$[0].content" with value "Le contenu du super article 18"
    And the json response has the key "$[0].url" with value "https://afup.org/url-18.html"
    And the json response has the key "$[1].url" with value "https://afup.org/url-19.html"
    And the json response has the key "$[2].url" with value "https://afup.org/url-flux-1-avec-slash.html"
    And the json response has the key "$[3].url" with value "https://afup.org/url-flux-1-sans-slash.html"

  Scenario: Get the list of feeds
    Given I am on "/planete-php-api/feeds"
    Then the response status code should be 200
    And the response should be in json
    And the json response has the key "$[0].name" with value "Exemple avec un / à la fin"
    And the json response has the key "$[0].url" with value "https://example.com/"
