Feature: Administration - Partie Personnes physiques

  @reloadDbWithTestData
  Scenario: Accès à l'ajout d'une inscription
    Given I am logged in as admin and on the Administration
    And I follow "Personnes physiques"
    Then the ".content h2" element should contain "Personnes physiques"
    # ajout d'une personne physique
    When I follow "Ajouter"
    Then I should see "Ajouter une personne physique"
    Then The "user_edit[genre]" field should only contain the follow values '["Ne se prononce pas", "Femme", "Homme", "Non-binaire"]'
    # Paramètres : checkbox d'accès à l'admin décochée par défaut, et matrice d'accès
    # aux modules (Aucun / Lecteur / Gestionnaire) positionnée sur "Aucun" pour chaque module
    And the "user_edit[adminAccess]" checkbox should be unchecked
    And I should see "Accès aux modules"
    And the "user_edit_access_membres_0" checkbox should be checked
    And the "user_edit_access_veille_0" checkbox should be checked
    And the "user_edit_access_site_0" checkbox should be checked
    And the "user_edit_access_apero_0" checkbox should be checked
    And the "user_edit_access_event_0" checkbox should be checked
    And the "user_edit_access_compta_0" checkbox should be checked
    And the "user_edit_access_ag_0" checkbox should be checked
    And the "user_edit_access_planete_0" checkbox should be checked
    And the "user_edit_access_antennes_0" checkbox should be checked
    When I press "Ajouter"
    Then I should see "Cette valeur ne doit pas être vide"
    When I fill in "user_edit[lastname]" with "Mon nom"
    And I fill in "user_edit[firstname]" with "Mon prénom"
    And I fill in "user_edit[email]" with "testemail@provider.fr"
    And I fill in "user_edit[address]" with "32 rue des lilas"
    And I fill in "user_edit[zipcode]" with "69001"
    And I fill in "user_edit[city]" with "LYON"
    And I fill in "user_edit[username]" with "monlogin"
    And I select "2" from "user_edit[access][veille]"
    And I select "1" from "user_edit[access][site]"
    And I check "user_edit_specialRoles_1"
    And I press "Ajouter"

    Then I should not see "Cette valeur ne doit pas être vide"
    # les droits sont relus depuis les rôles de la personne
    When I am on "/admin/members/users/edit?id=3"
    Then the "user_edit_access_veille_2" checkbox should be checked
    And the "user_edit_access_site_1" checkbox should be checked
    And the "user_edit_access_membres_0" checkbox should be checked
    And the "user_edit_specialRoles_1" checkbox should be checked
    And the "user_edit_specialRoles_0" checkbox should be unchecked
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
