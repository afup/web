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
    # Publication à venir : pas de lien
    But the response should not contain "/talks/3-revolutionons-php"
    And the response should contain "<loc>https://apachephptest:80/talks/speakers/geoffrey-bachelet</loc>"
    And the response should contain "<loc>https://apachephptest:80/talks/speakers/</loc>"
    And the response should not contain "fR%5Bspeakers"
    And the response should contain "<loc>https://apachephptest:80/talks/evenements/forum</loc>"
    And the response should contain "<loc>https://apachephptest:80/talks/evenements/passed</loc>"
    And the response should contain "<loc>https://apachephptest:80/talks/evenements/</loc>"
    And the response should not contain "fR%5Bevent"
    And the response should contain "<loc>https://apachephptest:80/talks/</loc>"

  @reloadDbWithTestData
  Scenario: On accède sitemap video
    Given I am on "/sitemap.video.xml"
    And the response should contain "<loc>https://apachephptest:80/talks/1-jouons-tous-ensemble-a-un-petit-jeu</loc>"

  @reloadDbWithTestData
  Scenario: On accède sitemap news
    Given I am on "/sitemap.news.xml"
    And the response should contain "<loc>https://apachephptest:80/news/1-les-videos-du-forum-2018-en-ligne</loc>"
    # 16 actualités, 5 par page : les pages 2 à 4 ; la première est déclarée par sa route
    And the response should contain "<loc>https://apachephptest:80/news/?page=2</loc>"
    And the response should contain "<loc>https://apachephptest:80/news/?page=4</loc>"
    And the response should not contain "?page=1<"
    And the response should not contain "?page=5"
    # Une liste filtrée est une page de recherche, hors sitemap
    And the response should not contain "news_filters"

  @reloadDbWithTestData
  Scenario: On accède sitemap members
    Given I am on "/sitemap.members.xml"
    And the response should contain "<loc>https://apachephptest:80/profile/company/1-mycorp</loc>"

  @reloadDbWithTestData
  Scenario: On accède sitemap default
    Given I am on "/sitemap.default.xml"
    And the response should contain "<loc>https://apachephptest:80/</loc>"
    And the response should not contain "<loc>https://apachephptest:80/home</loc>"

  @reloadDbWithTestData
  Scenario: Le plan du site liste les pages événement et les index, pas les talks un par un
    Given I am on "/plan-du-site"
    Then the response status code should be 200
    And the texts of "section#conferences a[href^='/talks/']" should be, in this order:
      """
      Historique des conférences
      Tous les talks et vidéos
      Tous les événements
      Tous les speakers
      forum
      Un évènement du passé
      """
    And the response should contain "href=\"/talks/evenements/forum\""
    And the response should contain "href=\"/talks/evenements/passed\""
    And the response should contain "href=\"/talks/evenements/\""
    And the response should contain "href=\"/talks/speakers/\""
    And the response should not contain "href=\"/talks/1-jouons-tous-ensemble-a-un-petit-jeu\""
    And I should see "2020" in the "section#conferences section#evenements-2020 h3" element
    And I should see "Le site" in the "section#pages h2" element
    And I should see "Adhérer à l'AFUP" in the "section#adherer h2" element
    And I should see "Association" in the "section#association h2" element
    And the response should contain "href=\"/profile/company/1-mycorp\""
    And the response should contain "href=\"/news/1-les-videos-du-forum-2018-en-ligne\""

  @reloadDbWithTestData
  Scenario: Tous les liens du plan du site vers les talks répondent 200
    Given I am on "/plan-du-site"
    Then every link matching "#^(https?://[^/]+)?/talks/#" should respond with status 200

