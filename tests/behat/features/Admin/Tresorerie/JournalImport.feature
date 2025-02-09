Feature: Administration - Trésorerie - Journal import

  @reloadDbWithTestData
  Scenario: Test d'import du fichier
    Given I am logged in as admin and on the Administration
    When I follow "Journal"
    Then the ".content h2" element should contain "Journal"
    # On vérifie qu'on a aucune entrée pour la période
    When I select "2019-01-01 - 2019-12-31" from "id_periode"
    And I submit the form with name "forum"
    Then the current URL should match "/id_periode=13/"
    When I check "also_display_classifed_entries"
    And I press "Filtrer"
    Then the current URL should match "/id_periode=13/"
    And the current URL should match "/also_display_classifed_entries=on/"
    And I should see " Aucune ecriture"
    # On importe le fichier sur le crédit mutuel
    When I follow "Importer un fichier CSV"
    Then I should see "Import CSV"
    When I select "CMUT" from "banque"
    And I attach the file "test_credit_mutuel.csv" to "fichiercsv"
    And I press "Soumettre"
    Then I should see "Le fichier a été importé"
    # On vérifie que l'import s'est bien passé
    When I select "2019-01-01 - 2019-12-31" from "id_periode"
    And I submit the form with name "forum"
    Then the current URL should match "/id_periode=13/"
    When I check "also_display_classifed_entries"
    And I press "Filtrer"
    Then the current URL should match "/id_periode=13/"
    And the current URL should match "/also_display_classifed_entries=on/"
    And I should not see " Aucune ecriture"
    # Test meetup
    When I follow the button of tooltip "Modifier la ligne CB MEETUP ORG SUB FACT 190323 DONT FRAIS DE COMM. 1,89 EUR"
    Then I should see "Modifier une écriture"
    Then The "idevenement" field should have the following selected text "Association AFUP"
    Then The "idcategorie" field should have the following selected text "Meetup"
    # Test Twilio
    When I move backward one page
    When I follow the button of tooltip "Modifier la ligne CB Twilio SZ5KZ54M FACT 090223 DONT FRAIS DE COMM. 0,26 EUR"
    Then I should see "Modifier une écriture"
    Then The "idevenement" field should have the following selected text "Association AFUP"
    Then The "idcategorie" field should have the following selected text "Outils"
