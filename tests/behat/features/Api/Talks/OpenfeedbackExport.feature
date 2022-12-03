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
    And the response header "Content-Length" should match "#^1018#"