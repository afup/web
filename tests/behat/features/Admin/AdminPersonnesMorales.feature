Feature: Administration - Partie Personnes morales

  @reloadDbWithTestData
  Scenario: Accès à l'ajout d'une inscription
    Given I am logged in as admin and on the Administration
    And I follow "Personnes morales"
    Then the ".content h2" element should contain "Liste des personnes morales"
    # ajout d'une personne morale
    When I follow "Ajouter"
    Then I should see "Ajouter une personne morale"
    When I press "Soumettre"
    Then I should see "Raison sociale manquante"
    When I fill in "company_edit[companyName]" with "My Corp"
    When I fill in "company_edit[siret]" with "123456"
    And I fill in "company_edit[address]" with "45 rue de la défense"
    And I fill in "company_edit[zipCode]" with "75001"
    And I fill in "company_edit[city]" with "Paris"
    And I fill in "company_edit[lastName]" with "Nom contact"
    And I fill in "company_edit[firstName]" with "Prénom contact"
    And I fill in "company_edit[email]" with "emailcontact@mycorp.fr"
    And I press "Soumettre"
    Then I should see "La personne morale a été ajoutée"
    And I should see "Liste des personnes morales"
    And I should see "My Corp"
    # modifier la personne morale
    When I follow the button of tooltip "Modifier la fiche de My Corp"
    Then the ".content h2" element should contain "Modifier une personne morale"
    When I fill in "company_edit[companyName]" with "My Corp modifié"
    And I fill in "company_edit[siret]" with "1234567"
    And I fill in "company_edit[address]" with "45 rue de la défense modifié"
    And I fill in "company_edit[zipCode]" with "75002"
    And I fill in "company_edit[city]" with "Paris modifié"
    And I fill in "company_edit[lastName]" with "Nom contact modifié"
    And I fill in "company_edit[firstName]" with "Prénom contact modifié"
    And I fill in "company_edit[email]" with "emailcontact-modifie@mycorp.fr"
    And I press "Soumettre"
    Then I should see "La personne morale a été modifiée"
    And I should see "Liste des personnes morales"
    And  I should see "My Corp modifié"
    # supprimer la personne morale
    When I follow the button of tooltip "Supprimer la fiche de My Corp modifié"
    Then I should see "La personne morale a été supprimée"

