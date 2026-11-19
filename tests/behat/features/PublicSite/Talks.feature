Feature: Site Public - Talks

  @reloadDbWithTestData
  Scenario: Accès à la liste des vidéos
    Given I am on the homepage
    When I follow "Vidéos"
    Then I should see "Historique des conférences de l'AFUP"
    And the response should contain "<link rel=\"canonical\" href=\"https://apachephptest:80/talks/\" />"
    When I go to "/talks/?p=1"
    And the response should contain "<link rel=\"canonical\" href=\"https://apachephptest:80/talks/\" />"
    When I go to "/talks/?q=&hPP=7&idx=afup_talks&p=-1"
    And the response should contain "<link rel=\"canonical\" href=\"https://apachephptest:80/talks/\" />"

  @reloadDbWithTestData
  Scenario: Accès à la liste des vidéos d'un évènement en particulier
    When I go to "/talks/?fR[event.title][0]=AFUP Day 2019 Lyon"
    Then I should see "AFUP Day 2019 Lyon les vidéos"
    And the response should contain "<link rel=\"canonical\" href=\"https://apachephptest:80/talks/?fR%5Bevent.title%5D%5B0%5D=AFUP%20Day%202019%20Lyon\" />"
    When I go to "/talks/?q=&hPP=7&idx=afup_talks&p=0&fR[event.title][0]=AFUP Day 2019 Lyon"
    Then I should see "AFUP Day 2019 Lyon les vidéos"
    And the response should contain "<link rel=\"canonical\" href=\"https://apachephptest:80/talks/?fR%5Bevent.title%5D%5B0%5D=AFUP%20Day%202019%20Lyon\" />"


  @reloadDbWithTestData
  Scenario: Affichage des posts Bluesky sur la page d'un talk
    When I go to "/talks/2-rest-ou-graphql-exemples-illustres-avec-symfony-et-api-platform"
    Then I should see "Posts Bluesky"
    And the response should contain "3mjk3hmqxxe2d"

  @reloadDbWithTestData
  Scenario: Accès à la liste des vidéos d'un speaker
    When I go to "/talks/?fR[speakers.label][0]=Un Speaker"
    Then I should see "Les vidéos de Un Speaker"
    And the response should contain "<link rel=\"canonical\" href=\"https://apachephptest:80/talks/?fR%5Bspeakers.label%5D%5B0%5D=Un%20Speaker\" />"
    When I go to "/talks/?q=&hPP=7&idx=afup_talks&p=0&fR[event.title][0]=AFUP Day 2019 Lyon&fR[speakers.label][0]=Un Speaker"
    Then I should see "Les vidéos de Un Speaker"
    And the response should contain "<link rel=\"canonical\" href=\"https://apachephptest:80/talks/?fR%5Bspeakers.label%5D%5B0%5D=Un%20Speaker\" />"

  @reloadDbWithTestData
  Scenario: Un talk avec le bon slug répond 200
    Given I do not follow redirects
    When I go to "/talks/2-rest-ou-graphql-exemples-illustres-avec-symfony-et-api-platform"
    Then the response status code should be 200

  @reloadDbWithTestData
  Scenario: Un talk avec un mauvais slug redirige en 301 vers le slug courant
    Given I do not follow redirects
    When I go to "/talks/2-mauvais-slug"
    Then the response status code should be 301
    And the response header "Location" should match "#^(https?://[^/]+)?/talks/2-rest-ou-graphql-exemples-illustres-avec-symfony-et-api-platform$#"

  @reloadDbWithTestData
  Scenario: Le lien joind.in d'un talk avec un mauvais slug redirige en 301 vers le slug courant
    Given I do not follow redirects
    When I go to "/talks/2-mauvais-slug/joindin"
    Then the response status code should be 301
    And the response header "Location" should match "#^(https?://[^/]+)?/talks/2-rest-ou-graphql-exemples-illustres-avec-symfony-et-api-platform/joindin$#"

  @reloadDbWithTestData
  Scenario: Un talk inexistant ou non publié répond 404, quel que soit le slug
    Given I do not follow redirects
    When I go to "/talks/999999-mauvais-slug"
    Then the response status code should be 404
    When I go to "/talks/999999-mauvais-slug/joindin"
    Then the response status code should be 404
    When I go to "/talks/4-mauvais-slug"
    Then the response status code should be 404
    When I go to "/talks/4-mauvais-slug/joindin"
    Then the response status code should be 404
