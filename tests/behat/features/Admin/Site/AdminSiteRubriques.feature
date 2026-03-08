Feature: Administration - Partie Site

  Scenario: Un membre ne peut pas accéder à la gestion du site
    Given I am logged-in with the user "paul" and the password "paul"
    And I am on "/admin/site/rubriques"
    Then the response status code should be 403
    And I am on "/admin/site/rubriques/add"
    Then the response status code should be 403

  @reloadDbWithTestData
  Scenario: Ajout/modification/suppression d'une rubrique
    Given I am logged in as admin and on the Administration
    And I follow "Rubriques"
    Then I should see "Liste des rubriques"
    And I should see "Actualités"
    # ajout d'une rubrique
    When I follow "Ajouter"
    Then I should see "Ajouter une rubrique"
    When I fill in "rubrique[nom]" with "Évènement"
    And I fill in "rubrique[contenu]" with "contenu évènements"
    And I fill in "rubrique[raccourci]" with "evenements"
    And I select "En ligne" from "rubrique[etat]"
    And I press "Ajouter"
    Then I should see "Liste des rubriques"
    And the ".content table" element should contain "Évènement"
    # modification d'une rubrique
    When I follow "modifier_10"
    Then I should see "Modifier une rubrique"
    When I fill in "rubrique[nom]" with "Évènements"
    And I press "Modifier"
    Then I should see "Liste des rubriques"
    And the ".content table" element should contain "Évènements"
    # Les rubriques sont bien visibles sur la page de création d'un article
    When I follow "Articles"
    Then I should see "Liste des articles"
    When I follow "Ajouter"
    Then I should see "Ajouter un article"
    Then The "article[rubricId]" field should only contain the follow values '["Actualités", "Évènements"]'

    # suppression d'une rubrique
    When I follow "Rubriques"
    And I follow "supprimer_10"
    Then I should see "Liste des rubriques"
    But the ".content table" element should not contain "Évènements"
