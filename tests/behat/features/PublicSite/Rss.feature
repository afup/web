Feature: Site Public - Flux RSS

  @reloadDbWithTestData
  Scenario: On accède au flux RSS via le lien dans le footer
    Given I am on the homepage
    And I follow "Accédez au flux RSS de l'AFUP"
    Then the response header "Content-type" should match '#^text/xml; charset=UTF-8$#'
    And the response should contain "Le flux RSS de l'AFUP"
    And the response should contain "<rss version=\"2.0\">"
    And the response should contain "<title><![CDATA[Les vidéos des talks du Forum PHP 2018 sont disponibles]]></title>"
    And the response should contain "<guid>https://apachephptest:80/news/1-les-videos-du-forum-2018-en-ligne</guid>"
    And the response should contain "<h3>Un premier titre !</h3>"
    And the response should contain "Et un peu <em>de texte en italic</em>"
    And the response should contain "</rss>"
