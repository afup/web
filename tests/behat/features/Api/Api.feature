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
