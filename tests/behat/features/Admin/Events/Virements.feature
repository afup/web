Feature: Administration - Évènements - Virements en attente

  Scenario: Un membre ne peut pas accéder aux virements en attente
    Given I am logged-in with the user "paul" and the password "paul"
    And I am on "/admin/event/bankwires"
    Then the response status code should be 403

  @reloadDbWithTestData
  Scenario: Accès à la liste des virements
    Given I am logged in as admin and on the Administration
    # Ajout d'une inscription avec virement
    And I follow "Inscriptions"
    When I follow "Ajouter"
    When I select "2" from "ticket_admin_with_invoice[ticket][ticketTypeId]"
    And I fill in "ticket_admin_with_invoice[ticket][lastname]" with "Nom participant"
    And I fill in "ticket_admin_with_invoice[ticket][firstname]" with "Prénom participant"
    And I fill in "ticket_admin_with_invoice[ticket][email]" with "nomparticipant@gmail.com"
    And I select "2" from "ticket_admin_with_invoice[invoice][paymentType]"
    And I select "0" from "ticket_admin_with_invoice[ticket][status]"
    And I fill in "ticket_admin_with_invoice[invoice][lastname]" with "Nom facturation"
    And I fill in "ticket_admin_with_invoice[invoice][firstname]" with "Prénom facturation"
    And I fill in "ticket_admin_with_invoice[invoice][address]" with "Adresse facturation"
    And I fill in "ticket_admin_with_invoice[invoice][zipcode]" with "56532"
    And I fill in "ticket_admin_with_invoice[invoice][city]" with "ville facturation"
    And I fill in "ticket_admin_with_invoice[invoice][email]" with "nomparticipant@gmail.com"
    And I press "Soumettre"
    # Accès à la liste
    And I follow "Virements en attente"
    Then the ".content h2" element should contain "Virements en attente de réception"
    And I should see "Facture Société Montant Date Places"
    And I should see "Prénom participant Nom participant"
    # Marque le virement comme reçu
    Then I press "Virement reçu"
    And I should not see "Prénom participant Nom participant"
