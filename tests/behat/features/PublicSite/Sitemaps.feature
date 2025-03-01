Feature: Site Public - Sitemaps

  @reloadDbWithTestData
  Scenario: On accède sitemap XML global
    Given I am on "/sitemap.xml"
    And the response should contain "<loc>https://apachephptest:80/sitemap.talks.xml</loc>"
    And the response should contain "<loc>https://apachephptest:80/sitemap.video.xml</loc>"
    And the response should contain "<loc>https://apachephptest:80/sitemap.news.xml</loc>"
    And the response should contain "<loc>https://apachephptest:80/sitemap.members.xml</loc>"
    And the response should contain "<loc>https://apachephptest:80/sitemap.default.xml</loc>"

  @reloadDbWithTestData
  Scenario: On accède sitemap talks
    Given I am on "/sitemap.talks.xml"
    And the response should contain "<loc>https://apachephptest:80/talks/1-jouons-tous-ensemble-a-un-petit-jeu</loc>"

  @reloadDbWithTestData
  Scenario: On accède sitemap video
    Given I am on "/sitemap.video.xml"
    And the response should contain "<loc>https://apachephptest:80/talks/1-jouons-tous-ensemble-a-un-petit-jeu</loc>"

  @reloadDbWithTestData
  Scenario: On accède sitemap news
    Given I am on "/sitemap.news.xml"
    And the response should contain "<loc>https://apachephptest:80/news/1-les-videos-du-forum-2018-en-ligne</loc>"

  @reloadDbWithTestData
  Scenario: On accède sitemap members
    Given I am on "/sitemap.members.xml"
    And the response should contain "<loc>https://apachephptest:80/profile/company/1-mycorp</loc>"

  @reloadDbWithTestData
  Scenario: On accède sitemap default
    Given I am on "/sitemap.default.xml"
    And the response should contain "<loc>https://apachephptest:80/home</loc>"
