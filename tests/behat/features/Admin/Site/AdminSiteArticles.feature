Feature: Administration - Partie Site

  Scenario: Ajout d'un article avec le minimum d'info
    Given I am logged in as admin and on the Administration
    And I follow "Articles"
    Then I should see "Liste des articles"
    And I should see "Actualités"
    When I follow "Ajouter"
    Then I should see "Ajouter un article"
    And I fill in "titre" with "Le titre mini"
    And I fill in "contenu" with "Le contenu mini"
    And I fill in "raccourci" with "url-article-mini"
    And I select "Actualités" from "id_site_rubrique"
    And I select "9" from "position"
    And I press "Ajouter"
    When I should see "Liste des articles"
    Then the ".content table" element should contain "Le titre mini"

  @reloadDbWithTestData
  Scenario: Ajout/modification/suppression d'un article
    Given I am logged in as admin and on the Administration
    And I follow "Articles"
    Then I should see "Liste des articles"
    And I should see "Actualités"

    # ajout d'un article
    When I follow "Ajouter"
    Then I should see "Ajouter un article"
    And I fill in "titre" with "Le titre de l'article"
    And I fill in "chapeau" with "Le chapeau de l'article"
    And I fill in "contenu" with "Le contenu de l'article"
    And I fill in "raccourci" with "url-article"
    And I select "Actualités" from "id_site_rubrique"
    And I select "Personne Paul" from "id_personne_physique"
    And I select "9" from "position"
    And I select "En ligne" from "etat"
    And I select "Associatif" from "theme"
    And I select "forum" from "id_forum"
    And I press "Ajouter"
    When I should see "Liste des articles"
    Then the ".content table" element should contain "Le titre de l'article"
    # vérification de l'article sur le site publique
    When I go to "/news/16-url-article"
    Then I should see "Le titre de l'article"
    Then I should see "Le chapeau de l'article"
    Then I should see "Le contenu de l'article"

    # modification d'un article
    When I follow "Administration"
    And I follow "Articles"
    Then I follow "modifier_2"
    And I should see "Modifier un article"
    And I fill in "titre" with "Le titre de l'article modifié"
    And I press "Modifier"
    And I should see "Liste des article"
    Then the ".content table" element should contain "Le titre de l'article modifié"

    # suppression d'un article
    When I follow "Articles"
    And I follow "supprimer_2"
    Then I should see "Liste des articles"
    But the ".content table" element should not contain "Le titre de l'article modifié"

  Scenario: Le raccourci ne doit pas contenir d'espace
    Given I am logged in as admin and on the Administration
    And I follow "Articles"
    Then I should see "Liste des articles"
    When I follow "Ajouter"
    And I fill in "raccourci" with "un mauvais raccourci"
    And I press "Ajouter"
    Then I should see "Ne doit pas contenir d'espace"

  Scenario: Ajout d'un article avec des émojis
    Given I am logged in as admin and on the Administration
    And I follow "Articles"
    When I follow "Ajouter"
    And I fill in "titre" with "Le titre qui envoi 🚀"
    And I fill in "contenu" with "Pour un contenu rigolo 🤣"
    And I fill in "raccourci" with "url-article-emojis"
    And I select "Actualités" from "id_site_rubrique"
    And I select "9" from "position"
    And I press "Ajouter"
    When I should see "Liste des articles"
    Then the ".content table" element should contain "Le titre qui envoi 🚀"
