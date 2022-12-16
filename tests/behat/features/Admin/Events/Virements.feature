Feature: Administration - Évènements - Virements en attente

  @reloadDbWithTestData
  Scenario: Accès à la liste des virements
    Given I am logged in as admin and on the Administration
    # Ajout d'une inscription avec virement
    And I follow "Inscriptions"
    When I follow "Ajouter"
    Then The "civilite" field should only contain the follow values '["M.", "Mme"]'
    When I select "2" from "type_inscription"
    And I fill in "nom" with "Nom participant"
    And I fill in "prenom" with "Prénom participant"
    And I fill in "email" with "nomparticipant@gmail.com"
    And I select "2" from "type_reglement"
    And I press "Soumettre"
    # Accès à la liste
    And I follow "Virements en attente"
    Then the ".content h2" element should contain "Virements en attente de réception"
    And I should see "Facture Société Montant Date Places"
    And I should see "Prénom participant Nom participant"
    # Marque le virement comme reçu
    Then I press "Virement reçu"
    And I should not see "Prénom participant Nom participant"
