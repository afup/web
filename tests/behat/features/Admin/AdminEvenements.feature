Feature: Administration - Partie Evenements

  @reloadDbWithTestData
  Scenario: Accès à la liste à l'ajout d'une inscription
    Given I am logged in as admin and on the Administration
    And I follow "Inscriptions"
    Then the ".content h2" element should contain "Inscriptions"
    # ajout d'une rubrique
    When I follow "Ajouter"
    Then I should see "Ajouter une inscription pour le forum"
    Then The "civilite" field should only contain the follow values '["M.", "Mme", "Mlle"]'
    When I select "2" from "type_inscription"
    And I fill in "nom" with "Nom participant"
    And I fill in "prenom" with "Prénom participant"
    And I fill in "email" with "nomparticipant@gmail.com"
    And I select "2" from "type_reglement"
    And I press "Soumettre"
    Then I should see "L'inscription a été ajoutée"
    And I should see "Inscriptions"
    And I should see "Prénom participant Nom participant"
