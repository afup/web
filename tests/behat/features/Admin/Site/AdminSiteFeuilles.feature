Feature: Administration - Partie Site

  Scenario: Un membre ne peut pas accéder à la gestion des feuilles du site
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
  Scenario: Ajout d'une feuille avec des dates de publication
    Given I am logged in as admin and on the Administration
    And I follow "Feuilles"
    When I follow "Ajouter"
    Then I should see "Ajouter une feuille"
    And I should see "Date de début de publication"
    And I should see "Date de fin de publication"
    And I fill in "feuille[nom]" with "Feuille avec dates"
    And I fill in "feuille[lien]" with "http://lien"
    And I fill in "feuille[dateDebutPublication]" with "2026-03-01"
    And I fill in "feuille[dateFinPublication]" with "2027-12-31"
    And I press "Ajouter"
    Then I should see "Liste des feuilles"
    And the ".content table" element should contain "01-03-2026"
    And the ".content table" element should contain "31-12-2027"

  @reloadDbWithTestData
  Scenario: Ajout/modification/suppression d'une feuille
    Given I am logged in as admin and on the Administration
    And I follow "Feuilles"
    Then I should see "Liste des feuilles"
    And I should see "Accueil"
    # ajout d'une feuille
    When I follow "Ajouter"
    Then I should see "Ajouter une feuille"
    When I fill in "feuille[nom]" with "Feuille test"
    And I fill in "feuille[lien]" with "http://lien"
    And I fill in "feuille[imageAlt]" with "Texte image alt"
    And I press "Ajouter"
    Then I should see "Liste des feuilles"
    And the ".content table" element should contain "Feuille test"
    # modification d'une feuille
    When I follow "modifier_1013"
    Then I should see "Modifier une feuille"
    And I should see "Feuille test"
    And the "feuille[lien]" field should contain "http://lien"
    And the "feuille[imageAlt]" field should contain "Texte image alt"
    When I fill in "feuille[nom]" with "Feuille modifiée"
    And I press "Modifier"
    Then I should see "Liste des feuilles"
    And the ".content table" element should contain "Feuille modifiée"
    # suppression d'une feuille
    When I follow "Feuilles"
    And I follow "supprimer_1013"
    Then I should see "Liste des feuilles"
    But the ".content table" element should not contain "Feuille test"
    # Modification d'une feuille avec une image
    When I follow "Feuilles"
    Then I should see "Liste des feuilles"
    When I follow the button of tooltip "Modifier la feuille La VEILLE de l'AFUP"
    Then I should see "Modifier une feuille"
    And the "feuille[nom]" field should contain "la VEILLE de l'AFUP"
    And I should see "Actuellement : /templates/site/images/tech-letter.png"
    When I attach the file "tech-letter2.png" to "feuille[image]"
    And I press "Modifier"
    Then I should see "La feuille La VEILLE de l'AFUP a été modifiée"
    # Modification d'une feuille sans changement pour vérifier que l'image n'est pas surchargée
    Then I should see "Liste des feuilles"
    And I should see an image with source "../../templates/site/images/tech-letter2.png"
    When I follow the button of tooltip "Modifier la feuille La VEILLE de l'AFUP"
    Then I should see "Actuellement : /templates/site/images/tech-letter2.png"
    When I press "Modifier"
    Then I should see "La feuille La VEILLE de l'AFUP a été modifiée"
    And I should see an image with source "../../templates/site/images/tech-letter2.png"


