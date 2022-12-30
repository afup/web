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
    Examples:
      | titre | speaker | id_joindin |
      | Jouons tous ensemble à un petit jeu Geoffrey Bachelet | Geoffrey Bachelet - AFUP(@ubermuda) | (JOIND.IN=24041) |