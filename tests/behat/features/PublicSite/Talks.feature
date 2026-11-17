Feature: Site Public - Talks

  @reloadDbWithTestData
  Scenario: Accès à la liste des vidéos
    Given I am on the homepage
    When I follow "Vidéos"
    Then I should see "Historique des conférences de l'AFUP"
    And the response should contain "<link rel=\"canonical\" href=\"https://apachephptest:80/talks/\" />"
    And the response should not contain "<meta name=\"robots\""
    When I go to "/talks/?p=0"
    Then the response should contain "<link rel=\"canonical\" href=\"https://apachephptest:80/talks/\" />"
    And the response should not contain "<meta name=\"robots\""

  @reloadDbWithTestData
  Scenario: Accès à la liste des vidéos d'un évènement en particulier
    When I go to "/talks/?fR[event.title][0]=forum"
    Then the response status code should be 200
    And I should see "forum les vidéos"
    And the response should contain "<link rel=\"canonical\" href=\"https://apachephptest:80/talks/?fR%5Bevent.title%5D%5B0%5D=forum\" />"
    And the response should not contain "<meta name=\"robots\""

  @reloadDbWithTestData
  Scenario: Évènement sans talk publié
    When I go to "/talks/?fR[event.title][0]=AFUP Day Lyon"
    Then the response status code should be 404

  @reloadDbWithTestData
  Scenario: Évènement inconnu
    When I go to "/talks/?fR[event.title][0]=AFUP Day 2019 Lyon"
    Then the response status code should be 404

  @reloadDbWithTestData
  Scenario Outline: Une recherche, une page ou un filtre de la liste des vidéos n'est pas indexé
    When I go to "<url>"
    Then the response status code should be 200
    And the response should contain "<meta name=\"robots\" content=\"noindex, follow\">"
    And the response should contain "<link rel=\"canonical\" href=\"https://apachephptest:80/talks/<canonical>\" />"

    Examples:
      | url                                                                                  | canonical                                      |
      | /talks/?q=symfony                                                                    |                                                |
      | /talks/?p=1                                                                          |                                                |
      | /talks/?hPP=7                                                                        |                                                |
      | /talks/?idx=afup_talks                                                               |                                                |
      | /talks/?is_v=1                                                                       |                                                |
      | /talks/?dFR[type.label][0]=Conférence                                                |                                                |
      | /talks/?nR[event.start_date][>=][0]=0                                                |                                                |
      | /talks/?fE[type.label][0]=Atelier                                                    |                                                |
      | /talks/?hFR[category][0]=php                                                         |                                                |
      | /talks/?tR=php                                                                       |                                                |
      | /talks/?fR[has_video][0]=true                                                        |                                                |
      | /talks/?q=&hPP=7&idx=afup_talks&p=0&fR[event.title][0]=forum                         | ?fR%5Bevent.title%5D%5B0%5D=forum              |
      | /talks/?fR[event.title][0]=forum&fR[speakers.label][0]=Geoffrey BACHELET             | ?fR%5Bspeakers.label%5D%5B0%5D=Geoffrey%20BACHELET |
      | /talks/?fR[speakers.label][0]=Geoffrey BACHELET&fR[speakers.label][1]=Adrien GALLOU  | ?fR%5Bspeakers.label%5D%5B0%5D=Geoffrey%20BACHELET |

  @reloadDbWithTestData
  Scenario: Affichage des posts Bluesky sur la page d'un talk
    When I go to "/talks/2-rest-ou-graphql-exemples-illustres-avec-symfony-et-api-platform"
    Then I should see "Posts Bluesky"
    And the response should contain "3mjk3hmqxxe2d"

  @reloadDbWithTestData
  Scenario: Accès à la liste des vidéos d'un speaker
    When I go to "/talks/?fR[speakers.label][0]=Geoffrey BACHELET"
    Then the response status code should be 200
    And I should see "Les vidéos de Geoffrey BACHELET"
    And the response should contain "<link rel=\"canonical\" href=\"https://apachephptest:80/talks/?fR%5Bspeakers.label%5D%5B0%5D=Geoffrey%20BACHELET\" />"
    And the response should not contain "<meta name=\"robots\""

  @reloadDbWithTestData
  Scenario: Libellé de speaker avec des espaces superflus
    When I go to "/talks/?fR[speakers.label][0]=%20Geoffrey%20%20%20BACHELET%20"
    Then the response status code should be 200
    And the response should not contain "<meta name=\"robots\""

  @reloadDbWithTestData
  Scenario: Speaker sans talk publié
    When I go to "/talks/?fR[speakers.label][0]=John DOE"
    Then the response status code should be 404

  @reloadDbWithTestData
  Scenario: Speaker inconnu
    When I go to "/talks/?fR[speakers.label][0]=Un Speaker"
    Then the response status code should be 404

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
