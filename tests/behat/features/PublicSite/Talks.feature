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

  @reloadDbWithTestData
  Scenario: Les liens vers joind.in d'un talk ne sont pas suivis par les robots
    When I go to "/talks/1-jouons-tous-ensemble-a-un-petit-jeu"
    Then I should see "Fiche joind.in"
    And all links with href containing "/joindin" should have rel containing "nofollow"

  Scenario: Le robots.txt interdit le crawl des redirections joind.in
    When I go to "/robots.txt"
    Then the response should contain the html
    """
    User-agent: *
    Disallow: /talks/*/joindin
    """

  @reloadDbWithTestData
  Scenario: Données structurées VideoObject sur la page d'un talk avec vidéo
    When I go to "/talks/1-jouons-tous-ensemble-a-un-petit-jeu"
    Then the response status code should be 200
    And the JSON-LD "VideoObject" should have the following properties:
      | @context     | https://schema.org                                                                                                                                                                                                                                                                     |
      | name         | Jouons tous ensemble à un petit jeu                                                                                                                                                                                                                                                    |
      | description  | Hey ! Tu as participé au quiz du #SuperApéroPHP ? Tu te demandes : mais comment ont-ils fait ? Ne cherche pas plus loin, je te propose de nous retrouver pour un petit jeu et de découvrir ensemble l'envers du décor ! Au programme : une session interactive avec Laravel / Laravel Echo / Redis et Socket.IO. |
      | thumbnailUrl | https://img.youtube.com/vi/MseSkWbhxV8/hqdefault.jpg                                                                                                                                                                                                                                   |
      | embedUrl     | https://www.youtube.com/embed/MseSkWbhxV8                                                                                                                                                                                                                                              |
      | contentUrl   | https://www.youtube.com/watch?v=MseSkWbhxV8                                                                                                                                                                                                                                            |
      | inLanguage   | fr                                                                                                                                                                                                                                                                                     |
      | transcript   | Merci. Merci. Bonjour, bonjour à toutes et à tous.                                                                                                                                                                                                                                     |
    And the JSON-LD "VideoObject" property "uploadDate" should match "/^\d{4}-\d{2}-\d{2}T09:00:00\+0[12]:00$/"

  @reloadDbWithTestData
  Scenario: Pas de VideoObject sur la page d'un talk sans vidéo
    When I go to "/talks/6-revolutionons-php"
    Then the response status code should be 200
    And the response should not contain a JSON-LD "VideoObject"
