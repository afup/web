Feature: Administration - Trésorerie - Journal

  @reloadDbWithTestData
  Scenario: Journal ajouter une dépense validation
    Given I am logged in as admin and on the Administration
    When I follow "Journal"
    Then the ".content h2" element should contain "Journal"
    When I follow "Ajouter"
    And I press "Ajouter"
    Then I should see "Type d'opération manquant"
    And I should see "Evenement manquant"
    And I should see "Type de compte manquant"
    And I should see "Montant manquant"

  Scenario: Journal dépense/recette
    Given I am logged in as admin and on the Administration
    When I follow "Journal"

    # Ajout d'une dépense
    When I follow "Ajouter"
    And I select "1" from "idoperation"
    And I select "1" from "idcompte"
    And I select "5" from "idevenement"
    And I select "1" from "date_saisie[d]"
    And I select "1" from "date_saisie[F]"
    And I select "26" from "idcategorie"
    And I fill in "nom_frs" with "Fournisseur"
    And I fill in "tva_intra" with "FR4412345"
    And I fill in "numero" with "001"
    And I fill in "description" with "Description dépense 001"
    And I fill in "montant" with "12000"
    And I fill in "comment" with "Commentaire dépense 001"
    And I press "Ajouter"
    Then I should see "L'écriture a été ajoutée"
    And I should see "01/01/"
    And I should see "Compte courant"
    And I should see "Administratif"
    And I should see "-12 000,00"
    And I should see "Description dépense 001"
    And I follow the button of tooltip "Modifier la ligne Description dépense 001"
    And I fill in "montant" with "12001"
    When I press "Modifier"
    Then I should see "L'écriture a été modifiée"
    And I should see "-12 001,00"

    # Ajout d'une recette
    When I follow "Ajouter"
    And I select "2" from "idoperation"
    And I select "1" from "idcompte"
    And I select "5" from "idevenement"
    And I select "2" from "date_saisie[d]"
    And I select "1" from "date_saisie[F]"
    And I select "26" from "idcategorie"
    And I fill in "nom_frs" with "Fournisseur"
    And I fill in "tva_intra" with "FR6612345"
    And I fill in "numero" with "001"
    And I fill in "description" with "Description recette 001"
    And I fill in "montant" with "12000"
    And I fill in "comment" with "Commentaire recette 001"
    And I press "Ajouter"
    Then I should see "L'écriture a été ajoutée"
    And I should see "02/01/"
    And I should see "Compte courant"
    And I should see "Administratif"
    And I should see "12 000,00"
    And I should see "Description recette 001"
    And I follow the button of tooltip "Modifier la ligne Description recette 001"
    And I fill in "montant" with "12002"
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

    # Export Excel
    When I follow "Exporter la période en CSV"
    Then the response header "Content-disposition" should match '#^attachment; filename="AFUP_(.*)_journal_from(.*).csv"#'
