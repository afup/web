Feature: Administration - Évènements - Planning des sessions

  @reloadDbWithTestData
  Scenario Outline: Accès au planning des session(s)
    Given I am logged in as admin and on the Administration
    And I follow "Planning"
    Then the ".content h2" element should contain "Planning"
    And I should see "3 SESSION(S)"
    And I should see "<titre>"
    And I should see "<speaker>"
    And I should see "<id_joindin>"

    # modifier la programmation  la session
    When I follow "Modifier la session Jouons tous ensemble à un petit jeu"
    Then I should see "Modifier les horaires d'une session pour le forum"
    And I select "10" from "debut[H]"
    And I select "30" from "debut[i]"
    And I select "11" from "fin[H]"
    And I select "30" from "fin[i]"
    And I press "Soumettre"
    Then I should see "Le planning de la session a été modifiée"
    And I should see "10:30 - 11:30 / La salle T"

    # supprimer la programmation de la session
    When I follow "Supprimer la session Jouons tous ensemble à un petit jeu"
    Then I should see "La programmation de la session a été supprimée"
    And I should not see "10:30 - 11:30 / La salle T"

    Examples:
      | titre | speaker | id_joindin |
      | Jouons tous ensemble à un petit jeu Geoffrey Bachelet | Geoffrey Bachelet - AFUP(@ubermuda) | (JOIND.IN=24041) |