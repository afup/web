Feature: Event pages - Ticketing - en anglais

  @reloadDbWithTestData
  Scenario: Achat de billet en anglais
    Given I am on "/event/forum/tickets?_locale=en"
    Then I should see "Ticketing: forum"
    Then The "purchase[tickets][0][civility]" field should only contain the follow values '["M.", "Mrs."]'
