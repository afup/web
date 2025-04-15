Feature: Administration - Partie Assemblée Générale CR

  Scenario: Un membre ne peut pas accéder aux comptes rendus de l'ssemblée générale
    Given I am logged-in with the user "paul" and the password "paul"
    And I am on "/admin/members/general_meeting/reports"
    Then the response status code should be 403

  @reloadDbWithTestData
  Scenario: Accède à la liste des CR
    Given I am logged in as admin and on the Administration
    And I follow "Assemblée générale"
    Then the ".content h2" element should contain "Assemblée générale"
    When I follow "Liste des comptes rendus"

    # Liste
    Then the ".content h2" element should contain "Assemblée générale - Comptes rendus"
    And I should see "2014-02-15_CR AG AFUP 2013-2014"

    # Ajout
    And I attach the file "test_file1.pdf" to "form_file"
    And I press "form[submit]"
    Then the ".content .message" element should contain "Le compte rendu a correctement été ajouté."
    And I should see "test_file1"

    # Suppression
    And I follow the button of tooltip "Supprimer le CR test_file1"
    Then the ".content .message" element should contain "Le compte rendu a correctement été supprimé."


