Feature: PlanetePHP API routes

  Scenario: Get the first page of articles
    Given I am on "/planete-php-api/articles"
    Then the response status code should be 200
    And the response header "Content-Type" should equal "application/json"
    And the response header "X-Pagination-Total" should equal 23
    And the response header "X-Pagination-Per-Page" should equal 20
    And the json response has the key "title" with value "Un titre"
    And the json response has the key "url" with value "https:\/\/afup.org\/url.html"
    And the json response has the key "author" with value "Un super auteur"
    And the json response has the key "content" with value "Le contenu du super article"

  Scenario: Get another page of articles
    Given I am on "/planete-php-api/articles?page=2"
    Then the response status code should be 200
    And the response header "Content-Type" should equal "application/json"
    And the response header "X-Pagination-Total" should equal 23
    And the response header "X-Pagination-Per-Page" should equal 20
    And the json response has the key "title" with value "Un titre 18"
    And the json response has the key "url" with value "https:\/\/afup.org\/url-18.html"
    And the json response has the key "author" with value "Un super auteur 18"
    And the json response has the key "content" with value "Le contenu du super article 18"

  Scenario: Get the list of feeds
    Given I am on "/planete-php-api/feeds"
    Then the response status code should be 200
    And the response header "Content-Type" should equal "application/json"
    And the json response has the key "name" with value "Un super site PHP"
    And the json response has the key "url" with value "https:\/\/afup.org"
