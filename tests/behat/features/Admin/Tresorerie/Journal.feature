Feature: Administration - Trésorerie - Journal

  @reloadDbWithTestData
  Scenario: Journal ajouter une dépense validation
    Given I am logged in as admin and on the Administration
    When I follow "Journal"
    Then the ".content h2" element should contain "Journal"
    When I follow "Ajouter"
    And I press "Ajouter"
    Then I should see "Type d'opération manquant"
    And I should see "Évènement manquant"
    And I should see "Catégorie manquante"
    And I should see "Montant manquant"

  Scenario: Journal dépense/recette
    Given I am logged in as admin and on the Administration
    When I follow "Journal"

    # Ajout d'une dépense
    When I follow "Ajouter"
    And I select "1" from "transaction[operationId]"
    And I select "1" from "transaction[accountId]"
    And I select "5" from "transaction[eventId]"
    And I fill in "transaction[accountingDate]" with "2026-01-01"
    And I select "26" from "transaction[categoryId]"
    And I fill in "transaction[vendorName]" with "Fournisseur"
    And I fill in "transaction[tvaIntra]" with "FR4412345"
    And I fill in "transaction[number]" with "001"
    And I fill in "transaction[description]" with "Description dépense 001"
    And I fill in "transaction[amount]" with "12000"
    And I fill in "transaction[comment]" with "Commentaire dépense 001"
    And I press "Ajouter"
    Then I should see "L'écriture a été ajoutée"
    And I should see "01/01/"
    And I should see "Compte courant"
    And I should see "Administratif"
    And I should see "-12 000,00"
    And I should see "Description dépense 001"
    And I follow the button of tooltip "Modifier la ligne Description dépense 001"
    And I fill in "transaction[amount]" with "12001"
    When I press "Modifier"
    Then I should see "L'écriture a été modifiée"
    And I should see "-12 001,00"

    # Ajout d'une recette
    When I follow "Ajouter"
    And I select "2" from "transaction[operationId]"
    And I select "1" from "transaction[accountId]"
    And I select "5" from "transaction[eventId]"
    And I fill in "transaction[accountingDate]" with "2026-01-02"
    And I select "26" from "transaction[categoryId]"
    And I fill in "transaction[vendorName]" with "Fournisseur"
    And I fill in "transaction[tvaIntra]" with "FR6612345"
    And I fill in "transaction[number]" with "001"
    And I fill in "transaction[description]" with "Description recette 001"
    And I fill in "transaction[amount]" with "12000"
    And I fill in "transaction[comment]" with "Commentaire recette 001"
    And I press "Ajouter"
    Then I should see "L'écriture a été ajoutée"
    And I should see "02/01/"
    And I should see "Compte courant"
    And I should see "Administratif"
    And I should see "12 000,00"
    And I should see "Description recette 001"
    And I follow the button of tooltip "Modifier la ligne Description recette 001"
    And I fill in "transaction[amount]" with "12002"
    When I press "Modifier"
    Then I should see "L'écriture a été modifiée"
    And I should see "12 002,00"

    # Onglet Dépenses
    When I follow "Dépenses"
    Then I should see "-12 001,00"
    And I should not see "12 002,00"

    # Onglet Recettes
    When I follow "Recette"
    Then I should see "12 001,00"
    And I should not see "-12 002,00"

    # Onglet Dépenses
    When I follow "Dépenses/Recettes"
    Then I follow the button of tooltip "Modifier la ligne Description dépense 001"
    Then the "transaction[description]" field should contain "Description dépense 001"
    And I should see "Soumettre et passer"
    And I should see "Passer"
    When I press "Passer"
    Then I should see "L'écriture n'a pas été modifiée"
    Then the "transaction[description]" field should contain "Description recette 001"
    When I press "Modifier"
    Then I should see "L'écriture a été modifiée"

    # Export Excel
    When I follow "Exporter la période en CSV"
    Then the response header "content-disposition" should match '#^attachment; filename=AFUP_(.*)_journal_from(.*).csv#'
    And the downloaded file should strictly be the same as "admin_journal_export.csv"

  @reloadDbWithTestData
  Scenario: Compte journal Télécharger les justificatifs groupés par mois
    Given I am logged in as admin and on the Administration
    When I follow "Journal"
    And I follow "Télécharger les justificatifs groupés par mois"
    Then the response header "Content-Disposition" should match '#filename="afup_justificatifs-(.*).zip"#'

  @reloadDbWithTestData
  Scenario: Compte journal Suppression d'une transaction
    Given I am logged in as admin and on the Administration
    When I am on "/admin/accounting/journal/list?periodId=15"
    And I follow the button of tooltip "Supprimer la fiche de PRLV SEPA ONLINE SAS SCW SCALEWAY "
    Then I should see "L'écriture a été supprimée"

  @reloadDbWithTestData
  Scenario: Compte journal Télécharger un justificatif
    Given I am logged in as admin and on the Administration
    When I follow "Journal"
    And I follow "Afficher aussi les entrées pointées"
    Then I should see "Une recette qui rapporte"
    When I follow "Télécharger le justificatif"
    Then the response header "Content-Disposition" should match '#^attachment; filename="test_file1.pdf"#'

  @reloadDbWithTestData
  Scenario: Compte journal afficher les entrées déjà pointées
    Given I am logged in as admin and on the Administration
    When I follow "Journal"
    And I follow "Afficher aussi les entrées pointées"
    Then I should see "Une recette qui rapporte"
    And I should see "Une dépense très utile"
    And I should see "Une dépense moins utile"

    # Export Excel
    When I follow "Exporter la période en CSV"
    Then the response header "content-disposition" should match '#^attachment; filename=AFUP_(.*)_journal_from(.*).csv#'
    And the downloaded file should strictly be the same as "admin_journal_export_reconciled.csv"
