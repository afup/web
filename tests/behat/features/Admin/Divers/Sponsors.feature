Feature: Administration - Sponsors/Partenaires

  @reloadDbWithTestData
  Scenario: Afficher Sponsors/Partenaires création / modification / suppression
    Given I am logged in as admin and on the Administration
    And I follow "Sponsors/Partenaires"
    Then I should see "Liste des sponsors/partenaires de forum"
    And I should see "Aucun inscrit"
    # Création
    When I follow "Ajouter un partenaire"
    Then I should see "Ajouter un partenaire"
     # Choix du Forum
    And I fill in "id_forum" with "1"
     # Choix du niveau Or
    And I fill in "id_niveau_partenariat" with "2"
    And I fill in "ranking" with "42"
    And I fill in "nom" with "Un partenaire en Or"
    And I fill in "presentation" with "La présentation du partenaire en Or"
    And I fill in "site" with "https://www.exemple.com"
    And I attach the file "avatar1.png" to "logo"
    When I press "Soumettre"
    # Liste
    Then the ".content .message" element should contain "Le partenaire a été ajouté"
    And I should see "forum Or Un partenaire en Or https://www.exemple.com avatar1.png 42"
    # Modification
    When I follow "Modifier le partenaire Un partenaire en Or"
    Then I should see "Partenaire de forum"
    And I fill in "ranking" with "73"
    And I fill in "nom" with "Un partenaire modifié"
    And I fill in "presentation" with "La présentation du partenaire modifié"
    And I fill in "site" with "https://www.exemple.com/updated"
    When I press "Soumettre"
    Then the ".content .message" element should contain "Le partenaire a été modifié"
    And I should see "forum Or Un partenaire modifié https://www.exemple.com/updated avatar1.png 73"
    # Suppression
    When I follow "Supprimer le partenaire Un partenaire modifié"
    Then the ".content .message" element should contain "Le partenaire a été supprimé"
    And I should see "Aucun inscrit"
