Feature: Administration - Partie GitHub Users

  Scenario: Un membre ne peut pas accéder aux utilisateurs GitHub
    Given I am logged-in with the user "paul" and the password "paul"
    And I am on "/admin/event/github-user/"
    Then the response status code should be 403

  @reloadDbWithTestData
  Scenario Outline: Accès à la liste des utilisateurs GitHub
    Given I am logged in as admin and on the Administration
    And I follow "Github Users"
    Then the ".content h2" element should contain "Github Users"
    When I follow "Ajouter"
    Then I should see "Ajouter un utilisateur GitHub"
    When I fill in "github_user[user]" with "<username>"
    And I <afupCrew> "github_user[afupCrew]"
    And I press "Sauvegarder"
    Then I should see "L'utilisateur github a été ajouté"
    And I should see "Github Users"
    And I should see "<username>"
    And I should see "<name>"
    Then I should see 4 "table tbody tr" elements
    # on vérifie que si on ajoute deux fois le même user, l'ajout est bloqué
    When I follow "Ajouter"
    Then I should see "Ajouter un utilisateur GitHub"
    When I fill in "github_user[user]" with "<username>"
    And I <afupCrew> "github_user[afupCrew]"
    And I press "Sauvegarder"
    Then I should see "Un autre compte existe avec cette information: <username>"

    Examples:
      | username  | afupCrew | name                 |
      | amenophis | uncheck  | Jérémy Leherpeur     |
      | vinceAmstoutz | check    | Vincent Amstoutz (afup) |
