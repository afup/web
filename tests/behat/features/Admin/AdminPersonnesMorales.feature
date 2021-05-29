Feature: Administration - Partie Personnes morales

  @reloadDbWithTestData
  Scenario: Accès à l'ajout d'une inscription
    Given I am logged in as admin and on the Administration
    And I follow "Personnes morales"
    Then the ".content h2" element should contain "Liste des personnes morales"
    # ajout d'une personne morale
    When I follow "Ajouter"
    Then I should see "Ajouter une personne morale"
    Then The "civilite" field should only contain the follow values '["M.", "Mme", "Mlle"]'
# TODO ça serait bien d'en créer une
