Feature: Valider les accès depuis un compte membre

  @reloadDbWithTestData
  Scenario: L'accès à une personne physique
    Given I am logged-in with the user "paul" and the password "paul"
    # Modification
    When I go to "/admin/members/users/edit?id=5"
    Then the response status code should be 403
    # Suppression
    When I go to "/admin/members/users/delete?id=5"
    Then the response status code should be 403
    # Cotisation
    When I go to "/pages/administration/index.php?page=cotisations&type_personne=0&id_personne=5"
    Then I should see "Vous n'avez pas le droit d'accéder à cette page"

  @reloadDbWithTestData
  Scenario: L'accès à une personne morale
    Given I am logged-in with the user "paul" and the password "paul"
    # Modification
    When I go to "/admin/members/companies/edit/2"
    Then the response status code should be 403
    # Cotisation
    When I go to "/pages/administration/index.php?page=cotisations&type_personne=1&id_personne=2"
    Then I should see "Vous n'avez pas le droit d'accéder à cette page"
    # Facture
    When I go to "/pages/administration/index.php?page=cotisations&action=telecharger_facture&type_personne=1&id_personne=2&id=7"
    Then I should see "Vous n'avez pas le droit d'accéder à cette page"
