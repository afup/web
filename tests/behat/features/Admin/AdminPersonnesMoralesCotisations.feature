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
    When I fill in "membership_fee[amount]" with "152"
    And I press "Modifier"
    Then I should see "La cotisation pour MyCorp a bien été modifiée"
    Then I should see "152.00"
    # supprimer la cotisation
    When I follow the button of tooltip "Supprimer la cotisation"
    Then I should see "La cotisation a été supprimée"
    # ajout d'une cotisation
    When I follow "Ajouter"
    Then I should see "Ajouter une cotisation"
    When I fill in "membership_fee[amount]" with "150"
    And I select "0" from "membership_fee[paymentType]"
    When I fill in "membership_fee[paymentDetails]" with "notes du réglement"
    When I fill in "membership_fee[clientReference]" with "42"
    And I fill in "membership_fee[startDate]" with "2022-03-05"
    And I fill in "membership_fee[endDate]" with "2023-03-05"
    And I press "Ajouter"
    Then I should see "La cotisation jusqu'au 05 mars 2023 pour MyCorp a bien été ajoutée"
    Then I should see "05/03/2022"
    Then I should see "05/03/2023"
    Then I should see "150.00"
    Then I should see "en espèces"
    # Modification du mode de paiement - chèque
    When I follow the button of tooltip "Modifier la cotisation"
    Then I should see "Modifier une cotisation"
    And I select "Chèques" from "membership_fee[paymentType]"
    And I fill in "membership_fee[paymentDetails]" with "42"
    And I press "Modifier"
    Then I should see "La cotisation pour MyCorp a bien été modifiée"
    Then I should see "par chèque n° 42"
    # Modification du mode de paiement - virement
    When I follow the button of tooltip "Modifier la cotisation"
    Then I should see "Modifier une cotisation"
    And I select "Virement" from "membership_fee[paymentType]"
    And I fill in "membership_fee[paymentDetails]" with "8342"
    And I press "Modifier"
    Then I should see "La cotisation pour MyCorp a bien été modifiée"
    Then I should see "par virement n° 8342"
    # Modification du mode de paiement - paiement en ligne
    When I follow the button of tooltip "Modifier la cotisation"
    Then I should see "Modifier une cotisation"
    And I select "En ligne" from "membership_fee[paymentType]"
    And I fill in "membership_fee[paymentDetails]" with ""
    And I press "Modifier"
    Then I should see "La cotisation pour MyCorp a bien été modifiée"
    Then I should see "en ligne"
