Feature: Administration - Trésorerie - Compte banques

  @reloadDbWithTestData
  Scenario: Compte banques voir le journal de banque
    Given I am logged in as admin and on the Administration
    When I follow "Compte banques"
    Then the ".content h2" element should contain "Journal de banque"
    Then I should see "16/10/2023 Carte Bleue Une recette qui rapporte 1 000,00"
    Then I should see "17/10/2023 Carte Bleue Une dépense très utile 500,00"
    Then I should see "500,00 Total débit"
    Then I should see "1 000,00 Total crédit"
    Then I should see "500,00 solde"

  Scenario: Compte banques Export Excel
    Given I am logged in as admin and on the Administration
    When I follow "Compte banques"
    And I follow "Export XLSX"
    Then the response header "Content-disposition" should match '#filename="compta_afup_(.*).xlsx"#'

#  Scenario: Compte banques Télécharger les justificatifs triés par mois
#    Given I am logged in as admin and on the Administration
#    When I follow "Compte banques"
#    And I follow "Télécharger les justificatifs triés par mois"
#    Then the response header "Content-disposition" should match '#???#'
