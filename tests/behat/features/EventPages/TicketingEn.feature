Feature: Event pages - Ticketing - en anglais

  @reloadDbWithTestData
  Scenario: On voit bien toute la page, même le footer
    Given I am on "/event/forum/tickets?_locale=en"
    Then I should see "Ticketting: forum"
    Then The "purchase[tickets][0][civility]" field should only contain the follow values '["M.", "Mrs."]'
