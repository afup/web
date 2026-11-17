Feature: Site Public - robots.txt

  Scenario: Le robots.txt déclare le sitemap
    Given I am on "/robots.txt"
    Then the response status code should be 200
    And the response should contain "Sitemap: https://afup.org/sitemap.xml"

  @reloadDbWithTestData
  Scenario: Le sitemap déclaré dans le robots.txt est un index de sitemaps
    Given I am on "/sitemap.xml"
    Then the response status code should be 200
    And the response should contain "<sitemapindex"
