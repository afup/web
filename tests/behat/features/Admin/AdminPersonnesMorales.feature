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
    When I press "Ajouter"
    Then I should see "Raison sociale manquante"
    When I fill in "raison_sociale" with "My Corp"
    And I fill in "adresse" with "45 rue de la défense"
    And I fill in "code_postal" with "75001"
    And I fill in "ville" with "Paris"
    And I fill in "nom" with "Nom contact"
    And I fill in "prenom" with "Prénom contact"
    And I fill in "email" with "emailcontact@mycorp.fr"
    And I press "Ajouter"
    Then I should see "La personne morale a été ajoutée"
    And I should see "Liste des personnes morales"
    And  I should see "My Corp"
