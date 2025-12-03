Feature: Antennes API routes

  @reloadDbWithTestData
  Scenario: Fetch one antenne
    Given the current date is "2025-11-08 23:59:59"
    And I am on "/api/antennes/lyon"
    Then the response status code should be 200
    And the response should be in json
    And the json response has the key "$.code" with value "lyon"
    And the json response has the key "$.label" with value "Lyon"
    And the json response has the key "$.logo.simple" with value "https://afup.org/images/offices/lyon.svg"
    And the json response has the key "$.links.meetup" with value "https://www.meetup.com/fr-FR/afup-lyon-php"
    And the json response has the key "$.links.linkedin" with value "https://www.linkedin.com/company/afup-lyon"
    And the json response has the key "$.links.bluesky" with value "https://bsky.app/profile/lyon.afup.org"
    And the json response has the key "$.next_meetup.title" with value "Le super meetup"
    And the json response has the key "$.next_meetup.date" with value "2025-11-08"
    And the json response has the key "$.next_meetup.location" with value "123 rue de la fleur"
    And the json response has the key "$.next_meetup.description" with value "Lorem ipsum dolor si amet"
    And the json response has the key "$.next_meetup.url" with value "https://www.meetup.com/fr-FR/afup-lyon-php/events/1"

  @reloadDbWithTestData
  Scenario: Date after next meetup
    Given the current date is "2025-11-09 00:00:00"
    And I am on "/api/antennes/lyon"
    Then the response status code should be 200
    And the response should be in json
    And the json response has no key "$.next_meetup"
