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
    And I fill in "user_edit[login]" with "monlogin"
    And I press "Ajouter"
    Then I should not see "Cette valeur ne doit pas être vide"
    # on vérifie qu'il est dans la liste
    When I follow "Personnes physiques"
    And I follow "Afficher aussi les personnes physiques inactives"
    Then I should see "Mon prénom"
    # on supprime la personne
    When I follow "supprimer_2"
    Then I should see "La personne physique a été supprimée"
