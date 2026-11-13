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


  @reloadDbWithTestData
  Scenario: Un gestionnaire Membres ne peut pas gérer un compte super admin
    Given I am logged-in with the user "gestionmembres" and the password "gestionmembres"
    # Une personne sans rôle reste gérable
    When I go to "/admin/members/users/edit?id=5"
    Then the response status code should be 200
    # mais la matrice des accès aux modules est verrouillée
    And I should see "Seul un super administrateur peut modifier les accès aux modules."
    # Le compte super admin (admin) n'est ni modifiable, ni supprimable, ni réinitialisable
    When I go to "/admin/members/users/edit?id=1"
    Then the response status code should be 403
    When I go to "/admin/members/users/delete?id=1"
    Then the response status code should be 403
    When I go to "/admin/members/users/reset-password?id=1"
    Then the response status code should be 403
    When I go to "/admin/members/users/send-welcome?id=1"
    Then the response status code should be 403
    # Les actions sont masquées dans la liste
    # le compte admin des seeds n'est pas actif (état "non finalisé")
    When I go to "/admin/members/users?filter=admin%40admin.fr&alsoDisplayInactive=1"
    Then I should see an "#cotisations_1" element
    And I should not see an "#modifier_1" element
    And I should not see an "#supprimer_1" element
    And I should not see an "#envoi_mdp_1" element
