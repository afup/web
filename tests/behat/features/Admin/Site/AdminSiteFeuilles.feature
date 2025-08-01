Feature: Administration - Partie Site

  Scenario: Un membre ne peut pas accéder à la gestion de planète PHP
    Given I am logged-in with the user "paul" and the password "paul"
    And I am on "/admin/site/feuilles"
    Then the response status code should be 403
    And I am on "/admin/site/feuilles/add"
    Then the response status code should be 403
    And I am on "/admin/site/feuilles/edit"
    Then the response status code should be 404
    And I am on "/admin/site/feuilles/edit/1"
    Then the response status code should be 403
    And I am on "/admin/site/feuilles/delete/1/foo"
    Then the response status code should be 403

  @reloadDbWithTestData
  Scenario: Ajout/modification/suppression d'une feuille
    Given I am logged in as admin and on the Administration
    And I follow "Feuilles"
    Then I should see "Liste des feuilles"
    And I should see "Accueil"
    # ajout d'une feuille
    When I follow "Ajouter"
    Then I should see "Ajouter une feuille"
    When I fill in "sheet[name]" with "Feuille test"
    And I fill in "sheet[link]" with "http://lien"
    And I fill in "sheet[imageAlt]" with "Texte image alt"
    And I press "Ajouter"
    Then I should see "Liste des feuilles"
    And the ".content table" element should contain "Feuille test"
    # modification d'une feuille
    When I follow "modifier_1013"
    Then I should see "Modifier une feuille"
    And I should see "Feuille test"
    And the "sheet[link]" field should contain "http://lien"
    And the "sheet[imageAlt]" field should contain "Texte image alt"
    When I fill in "sheet[name]" with "Feuille modifiée"
    And I press "Modifier"
    Then I should see "Liste des feuilles"
    And the ".content table" element should contain "Feuille modifiée"
    # suppression d'une feuille
    When I follow "Feuilles"
    And I follow "supprimer_1013"
    Then I should see "Liste des feuilles"
    But the ".content table" element should not contain "Feuille test"
