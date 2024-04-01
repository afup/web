Feature: Administration - Partie Assemblée Générale CR

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
    And I attach the file "test_file1.pdf" to "report_file"
    And I press "report[submit]"
    Then the ".content .message" element should contain "Le compte rendu a correctement été ajouté."
    And I should see "test_file1"

    # Suppression
    And I follow the button of tooltip "Supprimer le CR test_file1"
    Then the ".content .message" element should contain "Le compte rendu a correctement été supprimé."


