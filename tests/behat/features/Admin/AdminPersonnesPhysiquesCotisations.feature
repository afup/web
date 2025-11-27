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

  @reloadDbWithTestData
  Scenario: On test la gestion des cotisations d'une personne ratachée à une personne morale
    Given I am logged in as admin and on the Administration
    And I follow "Personnes physiques"
    Then I should see "Raoul Jan"
    And I should see tooltip "Accéder à la fiche de la personne morale"
    And I should see tooltip "Gérer les cotisations de la personne morale"
    When I follow the button of tooltip "Accéder à la fiche de la personne morale"
    Then I should see "Modifier une personne morale"

  @reloadDbWithTestData
  Scenario: On test la gestion des cotisations d'une personnes physique
    Given I am logged in as admin and on the Administration
    And I follow "Personnes physiques"
    Then I should see "Paul Personne"
    When I follow the button of tooltip "Gérer les cotisations de Personne Paul"
    Then I should see "Cotisations de Personne Paul"
    When I follow the button of tooltip "Modifier la cotisation"
    Then I should see "Modifier une cotisation"
    When I fill in "membership_fee[amount]" with "30"
    And I press "Modifier"
    Then I should see "La cotisation pour Personne Paul a bien été modifiée"
    Then I should see "30.00"
    # supprimer la cotisation
    When I follow the button of tooltip "Supprimer la cotisation"
    Then I should see "La cotisation a été supprimée"
    # ajout d'une cotisation
    When I follow "Ajouter"
    Then I should see "Ajouter une cotisation"
    When I fill in "membership_fee[amount]" with "25"
    And I select "0" from "membership_fee[paymentType]"
    When I fill in "membership_fee[paymentDetails]" with "notes du réglement"
    And I fill in "membership_fee[startDate]" with "2022-03-05"
    And I fill in "membership_fee[endDate]" with "2023-03-05"
    And I press "Ajouter"
    Then I should see "La cotisation jusqu'au 05 mars 2023 pour Personne Paul a bien été ajoutée"
    Then I should see "05/03/2022"
    Then I should see "05/03/2023"
    Then I should see "25.00"
    Then I should see "en espèces"
    # Modification du mode de paiement - chèque
    When I follow the button of tooltip "Modifier la cotisation"
    Then I should see "Modifier une cotisation"
    And I select "Chèques" from "membership_fee[paymentType]"
    And I fill in "membership_fee[paymentDetails]" with "42"
    And I press "Modifier"
    Then I should see "La cotisation pour Personne Paul a bien été modifiée"
    Then I should see "par chèque n° 42"
    # Modification du mode de paiement - virement
    When I follow the button of tooltip "Modifier la cotisation"
    Then I should see "Modifier une cotisation"
    And I select "Virement" from "membership_fee[paymentType]"
    And I fill in "membership_fee[paymentDetails]" with "8342"
    And I press "Modifier"
    Then I should see "La cotisation pour Personne Paul a bien été modifiée"
    Then I should see "par virement n° 8342"
    # Modification du mode de paiement - paiement en ligne
    When I follow the button of tooltip "Modifier la cotisation"
    Then I should see "Modifier une cotisation"
    And I select "En ligne" from "membership_fee[paymentType]"
    And I fill in "membership_fee[paymentDetails]" with ""
    And I press "Modifier"
    Then I should see "La cotisation pour Personne Paul a bien été modifiée"
    Then I should see "en ligne"
