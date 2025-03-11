Feature: Administration - Partie Personnes morales - cotisations

  @reloadDbWithTestData
  Scenario: On test le nom du fichier PDF de cotisation récupéré depuis l'admin d'une personne morale
    Given I am logged in as admin and on the Administration
    And I follow "Personnes morales"
    And I check "alsoDisplayInactive"
    And I press "Filtrer"
    Then I should see "MyCorp"
    When I follow the button of tooltip "Gérer les cotisations de MyCorp"
    Then I should see "Cotisations de MyCorp"
    When I follow the button of tooltip "Télécharger la facture"
    Then the response header "Content-disposition" should match '#attachment; filename="MyCorp_COTIS-#'

  @reloadDbWithTestData
  Scenario: On test la gestion des cotisations
    Given I am logged in as admin and on the Administration
    And I follow "Personnes morales"
    And I check "alsoDisplayInactive"
    And I press "Filtrer"
    Then I should see "MyCorp"
    When I follow the button of tooltip "Gérer les cotisations de MyCorp"
    Then I should see "Cotisations de MyCorp"
    When I follow the button of tooltip "Modifier la cotisation"
    Then I should see "Modifier une cotisation"
    When I fill in "montant" with "152"
    And I press "Modifier"
    Then I should see "La cotisation pour MyCorp a bien été modifiée"
    Then I should see "152.00"
    # supprimer la cotisation
    When I follow the button of tooltip "Supprimer la cotisation"
    Then I should see "La cotisation a été supprimée"
    # ajout d'une cotisation
    When I follow "Ajouter"
    Then I should see "Ajouter une cotisation"
    When I fill in "montant" with "150"
    And I select "0" from "type_reglement"
    When I fill in "informations_reglement" with "notes du réglement"
    When I fill in "reference_client" with "42"
    And I select "05" from "date_debut[d]"
    And I select "3" from "date_debut[F]"
    And I select "2022" from "date_debut[Y]"
    And I select "05" from "date_fin[d]"
    And I select "3" from "date_fin[F]"
    And I select "2023" from "date_fin[Y]"
    And I press "Ajouter"
    Then I should see "La cotisation jusqu'au 05 March 2023 pour MyCorp a bien été ajoutée"
    Then I should see "05/03/22"
    Then I should see "05/03/23"
    Then I should see "150.00"
    Then I should see "en espèces"
