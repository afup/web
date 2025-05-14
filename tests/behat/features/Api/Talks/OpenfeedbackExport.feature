Feature: Export talks Openfeedback - API

  @reloadDbWithTestData
  Scenario: Export d'un évènement inexistant
    Given I am on "/event/forumabcde/openfeedback.json"
    Then the response status code should be 404

  @reloadDbWithTestData
  Scenario: Export d'un évènement existant
    Given I am on "/event/forum/openfeedback.json"
    Then the response status code should be 200
    And the response header "Content-Type" should match "#^application/json#"
    And the response should contain "Jouons tous ensemble \u00e0 un petit jeu"
    And the response should contains date "-5 days 09:00:00"
    And the response should contains date "-1 days 20:00:00"
    And the response should contain "REST ou GraphQL ? Exemples illustr\u00e9s avec Symfony et API Platform"
