Feature: Espace membre, Assemblée Générale
  @reloadDbWithTestData
  Scenario: Non à jour de cotisation
    Given I am logged-in with the user "userexpire" and the password "userexpire"
    And I follow "Espace membre"
    Then I should see "Espace membre"
    When I follow "Indiquer ma présence"
    Then I should see "Ma participation à l'Assemblée Générale"
    And I should see "Vous ne pourrez donc pas voter lors de cette assemblée générale."

  @reloadDbWithTestData
  Scenario: Indiquer présent
    Given I am logged-in with the user "paul" and the password "paul"
    And I follow "Espace membre"
    Then I should see "Espace membre"
    When I follow "Indiquer ma présence"
    Then I should see "Ma participation à l'Assemblée Générale du"
    When I select "2" from "form[presence]"
    And I press "Confirmer"
    Then I should see "La présence et le pouvoir ont été modifiés"
