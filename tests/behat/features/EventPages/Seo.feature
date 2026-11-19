Feature: Event pages - SEO des pages CFP et billetterie

  @reloadDbWithTestData
  Scenario: La page CFP d'un événement à venir est indexable et titrée avec l'édition
    Given I am on "/event/forum"
    Then the response status code should be 200
    And the "title" element should contain "Appel à conférenciers – forum – AFUP"
    And the response should not contain "noindex"
    And the response should contain "<link rel=\"canonical\" href=\"https://apachephptest:80/event/forum\" />"

  Scenario: La page CFP d'un événement terminé n'est pas indexée et n'a pas de canonical
    Given I am on "/event/passed"
    Then the response status code should be 200
    And the "title" element should contain "Appel à conférenciers – Un évènement du passé – AFUP"
    And the response should contain "<meta name=\"robots\" content=\"noindex, follow\">"
    And the response should not contain "rel=\"canonical\""

  Scenario: La billetterie d'un événement à venir est indexable et titrée avec l'édition
    Given I am on "/event/forum/tickets"
    Then the response status code should be 200
    And the "title" element should contain "Billetterie – forum – AFUP"
    And the response should not contain "noindex"
    And the response should contain "<link rel=\"canonical\" href=\"https://apachephptest:80/event/forum/tickets\" />"

  Scenario: La billetterie d'un événement terminé n'est pas indexée et n'a pas de canonical
    Given I am on "/event/passed/tickets"
    Then the response status code should be 200
    And the "title" element should contain "Billetterie – Un évènement du passé – AFUP"
    And the response should contain "<meta name=\"robots\" content=\"noindex, follow\">"
    And the response should not contain "rel=\"canonical\""

  Scenario: La billetterie sponsor n'est pas indexée et n'a pas de canonical
    Given I am on "/event/forum/tickets/sponsor"
    Then the response status code should be 200
    And the "title" element should contain "Billetterie – forum – AFUP"
    And the response should contain "<meta name=\"robots\" content=\"noindex, follow\">"
    And the response should not contain "rel=\"canonical\""

  Scenario: La variante anglaise de la billetterie sponsor n'a pas de canonical vers une page noindex
    Given I am on "/event/forum/tickets/sponsor?_locale=en"
    Then the response status code should be 200
    And the "title" element should contain "Ticketing – forum – AFUP"
    And the response should contain "<meta name=\"robots\" content=\"noindex, follow\">"
    And the response should not contain "rel=\"canonical\""
