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
