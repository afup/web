Feature: Administration - Évènements - Salles

  Scenario: Un membre ne peut pas accéder aux Salles
    Given I am logged-in with the user "paul" and the password "paul"
    And I am on "/admin/event/room"
    Then the response status code should be 403

  @reloadDbWithTestData
  Scenario: On crée une nouvelle salle vide
    Given I am logged in as admin and on the Administration
    And I follow "Salles"
    Then the ".content h2" element should contain "Liste des salles pour forum"
    And I fill in "room_name" with "La salle ronde"
    When I press "Ajouter"
    And I should see "La salle \"La salle ronde\" a été ajoutée."
    And I should see "Liste des salles pour forum"
    And I fill in "edit_room_1_name" with "La grande salle ronde"
    When I press "Sauvegarder"
    And I should see "La salle \"La grande salle ronde\" a été sauvegardée."
    And I should see "Liste des salles pour forum"
    When I press "Supprimer"
    And I should see "La salle \"La grande salle ronde\" a été supprimée."
