Feature: Administration - Partie Personnes physiques

  @reloadDbWithTestData
  Scenario: Accès à l'ajout d'une inscription
    Given I am logged in as admin and on the Administration
    And I follow "Personnes physiques"
    Then the ".content h2" element should contain "Personnes physiques"
    # ajout d'une personne physique
    When I follow "Ajouter"
    Then I should see "Ajouter une personne physique"
    Then The "user_edit[civility]" field should only contain the follow values '["M.", "Mme"]'
    When I press "Ajouter"
    Then I should see "Cette valeur ne doit pas être vide"
    When I fill in "user_edit[lastname]" with "Mon nom"
    And I fill in "user_edit[firstname]" with "Mon prénom"
    And I fill in "user_edit[email]" with "testemail@provider.fr"
    And I fill in "user_edit[address]" with "32 rue des lilas"
    And I fill in "user_edit[zipcode]" with "69001"
    And I fill in "user_edit[city]" with "LYON"
    And I fill in "user_edit[username]" with "monlogin"
    And I press "Ajouter"

    Then I should not see "Cette valeur ne doit pas être vide"
    # on vérifie qu'il est dans la liste
    When I follow "Personnes physiques"
    And I check "alsoDisplayInactive"
    And I press "Filtrer"
    Then I should see "Mon prénom"
    # on supprime la personne
    When I follow "supprimer_3"
    Then I should see "La personne physique a été supprimée"

  @reloadDbWithTestData
  Scenario: Si on a deux personnes morales avc le même libellé, on affiche tout de même deux entrées dans le selecteur
    Given I am logged in as admin and on the Administration
    And I follow "Personnes morales"
    Then the ".content h2" element should contain "Liste des personnes morales"
    # ajout d'une personne morale
    When I follow "Ajouter"
    Then I should see "Ajouter une personne morale"
    When I fill in "company_edit[companyName]" with "My Corp"
    And I fill in "company_edit[siret]" with "123456"
    And I fill in "company_edit[address]" with "45 rue de la défense"
    And I fill in "company_edit[zipCode]" with "75001"
    And I fill in "company_edit[city]" with "Paris"
    And I fill in "company_edit[lastName]" with "Nom contact"
    And I fill in "company_edit[firstName]" with "Prénom contact"
    And I fill in "company_edit[email]" with "emailcontact@mycorp.fr"
    And I press "Soumettre"
    Then I should see "La personne morale a été ajoutée"
    And I should see "Liste des personnes morales"
    And  I should see "My Corp"
    # ajout d'une personne morale
    When I follow "Ajouter"
    Then I should see "Ajouter une personne morale"
    When I fill in "company_edit[companyName]" with "My Corp"
    And I fill in "company_edit[siret]" with "123456"
    And I fill in "company_edit[address]" with "45 rue de la défense"
    And I fill in "company_edit[zipCode]" with "75001"
    And I fill in "company_edit[city]" with "Paris"
    And I fill in "company_edit[lastName]" with "Nom contact"
    And I fill in "company_edit[firstName]" with "prénom contact"
    And I fill in "company_edit[email]" with "emailcontact@mycorp.fr"
    And I press "Soumettre"
    Then I should see "La personne morale a été ajoutée"
    And I should see "Liste des personnes morales"
    # vérification coté personnes physiques
    And I follow "Personnes physiques"
    Then the ".content h2" element should contain "Personnes physiques"
    # ajout d'une personne physique
    When I follow "Ajouter"
    Then I should see "Ajouter une personne physique"
    When I press "Ajouter"
    Then I should see "Cette valeur ne doit pas être vide"
    And the "user_edit[companyId]" field should only contain the follow values '["","Helios Aerospace (id : 2)","My Corp (id : 3)","My Corp (id : 4)","MyCorp (id : 1)"]'
