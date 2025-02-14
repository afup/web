Feature: Administration - Partie Personnes physiques - cotisations

  @reloadDbWithTestData
  Scenario: On test le nom du fichier PDF de cotisation récupéré depuis l'admin d'une personne physique
    Given I am logged in as admin and on the Administration
    And I follow "Personnes physiques"
    And I check "alsoDisplayInactive"
    And I press "Filtrer"
    Then I should see "userexpire"
    When I follow "cotisations_2"
    Then I should see "Cotisations de Jean Maurice"
    When I follow the button of tooltip "Télécharger la facture"
    Then the response header "Content-disposition" should equal 'attachment; filename="Maurice_COTIS-2018-198_13072018.pdf"'

  @reloadDbWithTestData
  Scenario: On test l'export CSV des "Personnes physiques en CSV"
    Given I am logged in as admin and on the Administration
    And I follow "Personnes physiques"
    Then I should see "Personnes physiques"
    Then I should see "Exports"
    And I follow "Toutes les personnes physiques en CSV"
    Then the response header "Content-disposition" should equal 'attachment; filename=export_personnes_physiques.csv'

  @reloadDbWithTestData
  Scenario: On test l'export CSV des "Personnes physiques actives en CSV"
    Given I am logged in as admin and on the Administration
    And I follow "Personnes physiques"
    Then I should see "Exports"
    Then I should see "Personnes physiques"
    And I follow "Export des personnes physiques actives en CSV"
    Then the response header "Content-disposition" should equal 'attachment; filename=export_personnes_physiques_actives.csv'

  @reloadDbWithTestData
  Scenario: On test l'export CSV des "Personnes physiques actives et company managers en CSV"
    Given I am logged in as admin and on the Administration
    And I follow "Personnes physiques"
    Then I should see "Exports"
    Then I should see "Personnes physiques"
    And I follow "Export des personnes physiques actives et company managers en CSV"
    Then the response header "Content-disposition" should equal 'attachment; filename=export_personnes_physiques_actives_managers.csv'
