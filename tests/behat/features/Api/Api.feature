Feature: API pour event

  @reloadDbWithTestData
  Scenario: Liste du programme sans filtre
    Given I am on "/blog/forum/program?apply-publication-date-filters=0&speakers-page-prefix=/forum/conferencier-e-s/"
    Then the response should contain "<script type=\"application/ld+json\">"
    And the response should contain "Jouons tous ensemble à un petit jeu"
    And the response should contain "REST ou GraphQL ? Exemples illustrés avec Symfony et API Platform"
    And the response should contain "Révolutionons PHP"

  @reloadDbWithTestData
  Scenario: Liste du programme avec filtre
    Given I am on "/blog/forum/program?apply-publication-date-filters=1&speakers-page-prefix=/forum/conferencier-e-s/"
    Then the response should contain "<script type=\"application/ld+json\">"
    And the response should contain "Jouons tous ensemble à un petit jeu"
    And the response should contain "REST ou GraphQL ? Exemples illustrés avec Symfony et API Platform"
    And the response should not contain "Révolutionons PHP"

  @reloadDbWithTestData
  Scenario: Planning
    Given I am on "/blog/forum/planning"
    Then the response should contain "La salle T"
    And the response should contain "<script type=\"application/ld+json\">"

  @reloadDbWithTestData
  Scenario: Speakers sans filtre
    Given I am on "/blog/forum/speakers?apply-publication-date-filters=0"
    Then the response should contain "<script type=\"application/ld+json\">"
    And the response should contain "Adrien GALLOU"
    And the response should contain "Geoffrey BACHELET"

  @reloadDbWithTestData
  Scenario: Speakers avec filtre
    Given I am on "/blog/forum/speakers?apply-publication-date-filters=1"
    Then the response should contain "<script type=\"application/ld+json\">"
    And the response should not contain "Adrien GALLOU"
    And the response should contain "Geoffrey BACHELET"

  @reloadDbWithTestData
  Scenario: Ical Talk 1
    Given I am on "/blog/talk_widget?ids=1"
    Then the response should contain "Jouons tous ensemble à un petit jeu"
    And the response should not contain "REST ou GraphQL ? Exemples illustrés avec Symfony et API Platform"
    And the response should contain "Geoffrey BACHELET"

  @reloadDbWithTestData
  Scenario: Ical Talk 1 et 2
    Given I am on "/blog/talk_widget?ids=1,2"
    Then the response should contain "Jouons tous ensemble à un petit jeu"
    And the response should contain "REST ou GraphQL ? Exemples illustrés avec Symfony et API Platform"
    And the response should contain "Geoffrey BACHELET"

  @reloadDbWithTestData
  Scenario: Le programme lie la vidéo de chaque talk publié qui en a une
    Given I am on "/blog/forum/program?apply-publication-date-filters=0"
    Then the response should contain "Voir la vidéo"
    And the response should contain "href=\"https://apachephptest:80/talks/1-jouons-tous-ensemble-a-un-petit-jeu\""
    And the response should contain "href=\"https://apachephptest:80/talks/2-rest-ou-graphql-exemples-illustres-avec-symfony-et-api-platform\""
    # Talk 3 : une vidéo, mais une date de publication future.
    And the response should contain "Révolutionons PHP"
    And the response should not contain "/talks/3-"

  @reloadDbWithTestData
  Scenario: Le programme ne lie pas un talk publié sans vidéo
    Given I am on "/blog/passed/program"
    Then the response should contain "Révolutionons PHP"
    And the response should not contain "/talks/6-"
    And the response should not contain "Voir la vidéo"

  @reloadDbWithTestData
  Scenario: Les speakers sont liés à leur page sur afup.org quand elle existe
    Given I am on "/blog/forum/speakers?apply-publication-date-filters=0"
    Then the response should contain "href=\"https://apachephptest:80/talks/speakers/geoffrey-bachelet\""
    # Adrien Gallou n'a aucun talk publié : sa page speaker n'existe pas.
    And the response should contain "Adrien GALLOU"
    And the response should not contain "/talks/speakers/adrien-gallou"

  @reloadDbWithTestData
  Scenario: Le widget talk lie le speaker à sa page sur afup.org
    Given I am on "/blog/talk_widget?ids=1&type=speaker"
    Then the response should contain "href=\"https://apachephptest:80/talks/speakers/geoffrey-bachelet\""
    And the response should not contain "href=\"\""
