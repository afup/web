Feature: Site Public - Flux RSS

  Scenario: On accède au flux RSS via le lien dans le footer
    Given I am on the homepage
    And I follow "Accédez au flux RSS de l'AFUP"
    Then the response header "Content-type" should match '#^text/xml; charset=UTF-8$#'
    And the response should contain "Le flux RSS de l'AFUP"
    And the response should contain "<rss version=\"2.0\">"
    And the response should contain "</rss>"
