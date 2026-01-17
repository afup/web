Feature: Administration - Trésorerie - Journal import

  @reloadDbWithTestData
  Scenario: Test d'import du fichier
    Given I am logged in as admin and on the Administration
    When I follow "Journal"
    Then the ".content h2" element should contain "Journal"
    # On vérifie qu'on a aucune entrée pour la période
    When I select "01/01/2019 - 31/12/2019" from "periodId"
    And I submit the form with name "periodId_selector"
    Then the current URL should match "/periodId=13/"
    When I check "with_reconciled"
    And I press "Filtrer"
    Then the current URL should match "/periodId=13/"
    And the current URL should match "/with_reconciled=on/"
    And I should see " Aucune ecriture"
    # On importe le fichier sur le crédit mutuel
    When I follow "Importer un fichier CSV"
    Then I should see "Import CSV"
    When I select "CMUT" from "transactions_import[bankAccount]"
    And I attach the file "test_credit_mutuel.csv" to "transactions_import[file]"
    And I press "Soumettre"
    Then I should see "Le fichier a été importé"
    # On vérifie que l'import s'est bien passé
    When I select "01/01/2019 - 31/12/2019" from "periodId"
    And I submit the form with name "periodId_selector"
    Then the current URL should match "/periodId=13/"
    When I check "with_reconciled"
    And I press "Filtrer"
    Then the current URL should match "/periodId=13/"
    And the current URL should match "/with_reconciled=on/"
    And I should not see " Aucune ecriture"
    # Test meetup
    When I follow the button of tooltip "Modifier la ligne CB MEETUP ORG SUB FACT 190323 DONT FRAIS DE COMM. 1,89 EUR"
    Then I should see "Modifier une écriture"
    Then The "transaction[eventId]" field should have the following selected text "Association AFUP"
    Then The "transaction[categoryId]" field should have the following selected text "Meetup"
    # Test Twilio
    When I move backward one page
    When I follow the button of tooltip "Modifier la ligne CB Twilio SZ5KZ54M FACT 090223 DONT FRAIS DE COMM. 0,26 EUR"
    Then I should see "Modifier une écriture"
    Then The "transaction[eventId]" field should have the following selected text "Association AFUP"
    Then The "transaction[categoryId]" field should have the following selected text "Outils"
