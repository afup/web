Feature: Event pages - Ticketing - en anglais

  @reloadDbWithTestData
  Scenario: Achat de billet en anglais
    Given I am on "/event/forum/tickets?_locale=en"
    Then I should see "Ticketing: forum"
    Then The "purchase[tickets][0][genre]" field should only contain the follow values '["Prefer not to say", "Woman", "Man", "Nonbinary"]'
