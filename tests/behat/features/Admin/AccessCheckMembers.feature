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
    When I go to "/admin/accounting/membership-fee/list/0/5"
    Then the response status code should be 403

  @reloadDbWithTestData
  Scenario: L'accès à une personne morale
    Given I am logged-in with the user "paul" and the password "paul"
    # Modification
    When I go to "/admin/members/companies/edit/2"
    Then the response status code should be 403
    # Cotisation
    When I go to "/admin/accounting/membership-fee/list/1/2"
    Then the response status code should be 403

    # Facture
    When I go to "/admin/accounting/membership-fee/download/1/2/7"
    Then the response status code should be 403

