Feature: Event pages - Ticketing

  @reloadDbWithTestData
  Scenario: On voit bien toute la page, même le footer
    Given I am on "/event/forum/tickets"
    Then I should see "Billetterie: forum"
    And I should see "Si vous rencontrez le moindre problème, n'hésitez pas à nous contacter à l'adresse bonjour [@] afup.org."
    Then The "purchase[tickets][0][civility]" field should only contain the follow values '["M.", "Mlle", "Mme"]'

