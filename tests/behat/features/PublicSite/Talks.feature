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
  Scenario: Fil d'Ariane de la page d'un talk
    When I go to "/talks/6-revolutionons-php"
    Then I should see "Accueil" in the "nav[aria-label^=Fil]" element
    And I should see "Conférences" in the "nav[aria-label^=Fil]" element
    And I should see "Un évènement du passé" in the "nav[aria-label^=Fil]" element
    And I should see "Révolutionons PHP" in the "nav[aria-label^=Fil] [aria-current=page]" element
    And the JSON-LD "BreadcrumbList" should have the following items:
      | position | name                  | item                                                                                    |
      | 1        | Accueil               | https://apachephptest:80/                                                               |
      | 2        | Conférences           | https://apachephptest:80/talks/                                                         |
      | 3        | Un évènement du passé | https://apachephptest:80/talks/?fR%5Bevent.title%5D%5B0%5D=Un%20%C3%A9v%C3%A8nement%20du%20pass%C3%A9 |
      | 4        | Révolutionons PHP     | https://apachephptest:80/talks/6-revolutionons-php                                      |

  @reloadDbWithTestData
  Scenario: Liens vers les autres talks depuis la page d'un talk
    When I go to "/talks/6-revolutionons-php"
    Then the page should contain at least 8 links to other talks
    And I should see "Keynote d'ouverture" in the "a[rel=prev]" element
    And I should see "Tester sans se ruiner" in the "a[rel=next]" element
    And I should see "Aussi au Un évènement du passé"
    And I should see "Observabilité avec OpenTelemetry"
    And I should see "Le typage statique en pratique"
    And I should see "Clôture : le futur de PHP"
    And I should see "Migrer une application legacy"
    And I should see "Autres talks de Geoffrey BACHELET"
    And I should see "Jouons tous ensemble à un petit jeu"
    And I should see "REST ou GraphQL ? Exemples illustrés avec Symfony et API Platform"
    And I should not see "Un talk pas encore publié"

  @reloadDbWithTestData
  Scenario: Premier et dernier talks du planning d'une édition
    When I go to "/talks/8-keynote-d-ouverture"
    Then I should not see "Talk précédent"
    And I should see "Révolutionons PHP" in the "a[rel=next]" element
    When I go to "/talks/7-cloture-le-futur-de-php"
    Then I should see "Le typage statique en pratique" in the "a[rel=prev]" element
    And I should not see "Talk suivant"
