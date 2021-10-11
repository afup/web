Feature: Administration - Partie Personnes physiques - cotisations

  @reloadDbWithTestData
  Scenario: On test le nom du fichier PDF de cotisation récupéré depuis l'admin
    Given I am logged in as admin and on the Administration
    And I follow "Personnes physiques"
    And I check "alsoDisplayInactive"
    And I press "Filtrer"
    Then I should see "userexpire"
    When I follow "cotisations_2"
    Then I should see "Cotisations de Jean Maurice"
    When I follow the button of tooltip "Télécharger la facture"
    Then the response header "Content-disposition" should equal 'attachment; filename="Maurice_COTIS-2018-198_13072018.pdf"'
