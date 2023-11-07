Feature: Planete PHP - RSS

  @reloadDbWithTestData
  Scenario: On accède bien au flux RSS
    Given I am on the homepage
    When I follow "RSS Feed"
    Then the response should contain "<title>planete php fr</title>"
    Then the response should contain "<title>Un titre</title>"
    Then the response should contain "<title>Un 2e titre</title>"
    Then the response should contain "</rss>"
