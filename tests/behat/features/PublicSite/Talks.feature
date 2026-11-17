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
    # Talk planifié dont la date de publication est à venir : ni page, ni lien dans le sitemap
    When I go to "/talks/3-revolutionons-php"
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
      | inLanguage   | fr                                                                                                                                                                                                                                                                                     |
      | transcript   | Merci. Merci. Bonjour, bonjour à toutes et à tous.                                                                                                                                                                                                                                     |
    And the JSON-LD "VideoObject" property "uploadDate" should match "/^\d{4}-\d{2}-\d{2}T09:00:00\+0[12]:00$/"
    And the JSON-LD "VideoObject" property "url" should match "#^https://[^/]+/talks/1-jouons-tous-ensemble-a-un-petit-jeu$#"

  @reloadDbWithTestData
  Scenario: Pas de VideoObject sur la page d'un talk sans vidéo
    When I go to "/talks/6-revolutionons-php"
    Then the response status code should be 200
    And the response should not contain a JSON-LD "VideoObject"
  Scenario: Page d'un speaker rendue côté serveur
    When I go to "/talks/speakers/geoffrey-bachelet"
    Then the response status code should be 200
    And the response should contain "<title>Talks de Geoffrey Bachelet – AFUP</title>"
    And I should see "Geoffrey Bachelet" in the "h1" element
    And I should see "Président 2018-2019 de l'AFUP"
    And the response should contain "<link rel=\"canonical\" href=\"https://apachephptest:80/talks/speakers/geoffrey-bachelet\"/>"
    And the response should contain "href=\"/talks/1-jouons-tous-ensemble-a-un-petit-jeu\""
    And the response should contain "href=\"/talks/2-rest-ou-graphql-exemples-illustres-avec-symfony-et-api-platform\""
    And the response should contain "href=\"/talks/6-revolutionons-php\""
    And I should see "Un évènement du passé"
    And the response should contain "href=\"/talks/evenements/passed\""
    And the response should contain "https://img.youtube.com/vi/MseSkWbhxV8/hqdefault.jpg"
    And the response should contain "/images/no_video.jpg"
    And the response should contain "href=\"https://phpc.social/@ubermuda\""
    And the JSON-LD "Person" should have the following properties:
      | name | Geoffrey Bachelet                                          |
      | url  | https://apachephptest:80/talks/speakers/geoffrey-bachelet  |
    And the JSON-LD "Person" property "sameAs" should contain "https://github.com/ubermuda"
    And the JSON-LD "Person" property "sameAs" should contain "https://phpc.social/@ubermuda"

  @reloadDbWithTestData
  Scenario: Page d'un speaker inconnu
    When I go to "/talks/speakers/speaker-inconnu"
    Then the response status code should be 404

  @reloadDbWithTestData
  Scenario: Un speaker non rattaché à une personne n'a pas de page
    Given I am logged in as admin and on the Administration
    And I am on "/admin/speaker/personne?id=1"
    When I press "Détacher" in the row of "foo@bar.baz"
    Then I should see "Le conférencier a été détaché de la personne"
    When I go to "/talks/speakers/geoffrey-bachelet"
    Then the response status code should be 404
    When I go to "/talks/speakers/"
    Then the response should not contain "/talks/speakers/geoffrey-bachelet"

  @reloadDbWithTestData
  Scenario: Index des speakers groupé par initiale
    When I go to "/talks/speakers/"
    Then the response status code should be 200
    And I should see "B" in the "section#speakers-B h2" element
    And the response should contain "href=\"/talks/speakers/geoffrey-bachelet\""

  @reloadDbWithTestData
  Scenario: L'ancienne facette d'un speaker redirige vers sa page
    Given I do not follow redirects
    When I go to "/talks/?fR[speakers.label][0]=Geoffrey BACHELET"
    Then the response status code should be 301
    And the response header "Location" should match "#^(https?://[^/]+)?/talks/speakers/geoffrey-bachelet$#"

  @reloadDbWithTestData
  Scenario: Une facette speaker combinée à d'autres paramètres reste une recherche
    Given I do not follow redirects
    When I go to "/talks/?fR[speakers.label][0]=Geoffrey BACHELET&p=1"
    Then the response status code should be 200
    And I should see "Les vidéos de Geoffrey BACHELET"

  @reloadDbWithTestData
  Scenario: La page d'un talk renvoie vers la page de ses speakers
    When I go to "/talks/1-jouons-tous-ensemble-a-un-petit-jeu"
    And I follow "Voir tous ses talks"
    Then the url should match "/talks/speakers/geoffrey-bachelet"
    And I should see "Geoffrey Bachelet" in the "h1" element

  @reloadDbWithTestData
  Scenario: Page d'un événement rendue côté serveur
    When I go to "/talks/evenements/forum"
    Then the response status code should be 200
    And the response should contain "<title>forum : les talks et vidéos – AFUP</title>"
    And I should see "forum : les talks et vidéos" in the "h1" element
    And I should see "à Paris"
    And the response should contain "href=\"https://event.afup.org/forum/\""
    And the texts of "main ol article h3" should be, in this order:
      """
      Jouons tous ensemble à un petit jeu
      REST ou GraphQL ? Exemples illustrés avec Symfony et API Platform
      """
    And I should not see "Révolutionons PHP"
    And the response should contain "https://img.youtube.com/vi/QhAToFl_Omo/hqdefault.jpg"
    And the response should contain "href=\"/talks/speakers/geoffrey-bachelet\""
    And the JSON-LD "ItemList" should have the following properties:
      | name          | forum : les talks et vidéos                     |
      | url           | https://apachephptest:80/talks/evenements/forum |
      | numberOfItems | 2                                               |
    And the "url" of each item of the JSON-LD "ItemList" property "itemListElement" should be, in this order:
      """
      https://apachephptest:80/talks/1-jouons-tous-ensemble-a-un-petit-jeu
      https://apachephptest:80/talks/2-rest-ou-graphql-exemples-illustres-avec-symfony-et-api-platform
      """

  @reloadDbWithTestData
  Scenario: Page d'un événement inconnu ou sans talk publié
    When I go to "/talks/evenements/evenement-inconnu"
    Then the response status code should be 404
    When I go to "/talks/evenements/afup-day-lyon"
    Then the response status code should be 404

  @reloadDbWithTestData
  Scenario: Index des événements groupé par année
    When I go to "/talks/evenements/"
    Then the response status code should be 200
    And I should see "2020" in the "section#evenements-2020 h2" element
    And the texts of "section#evenements-2020 li a" should be, in this order:
      """
      Un évènement du passé
      """
    And the texts of "section#evenements-2020 li p" should be, in this order:
      """
      1 talk · 0 vidéo
      """
    And the response should contain "href=\"/talks/evenements/forum\""
    And I should see "2 talks · 2 vidéos"
    And I should not see "AFUP Day Lyon"

  @reloadDbWithTestData
  Scenario: L'ancienne facette d'un événement redirige vers sa page
    Given I do not follow redirects
    When I go to "/talks/?fR[event.title][0]=forum"
    Then the response status code should be 301
    And the response header "Location" should match "#^(https?://[^/]+)?/talks/evenements/forum$#"

  @reloadDbWithTestData
  Scenario: Une facette événement combinée à d'autres paramètres reste une recherche
    Given I do not follow redirects
    When I go to "/talks/?fR[event.title][0]=forum&p=1"
    Then the response status code should be 200
    And I should see "forum les vidéos"

  @reloadDbWithTestData
  Scenario: Le bandeau de la page d'un talk renvoie vers la page de son événement
    When I go to "/talks/1-jouons-tous-ensemble-a-un-petit-jeu"
    Then the response should contain "href=\"https://apachephptest:80/talks/evenements/forum\""
    And the response should not contain "fR%5Bevent.title"
