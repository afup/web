Feature: Administration - Synthese des évènements

  @reloadDbWithTestData
  Scenario: Synthese des évènements de l'AG
    Given I am logged in as admin and on the Administration
    When I follow "Synthese évènement"
    Then the ".content h2" element should contain "Synthese des évènements"
    Then I select "5" from "idevnt"
    And I press "evt_submit"
    Then I should see "Assurances Une dépense très utile 500,00"
    Then I should see "Assurances Une recette qui rapporte 1 000,00"
    Then I should see "500,00 Total dépenses"
    Then I should see "1 000,00 Total recettes"
    Then I should see "500,00 Différence"

