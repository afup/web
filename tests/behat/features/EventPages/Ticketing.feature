Feature: Event pages - Ticketing

  @reloadDbWithTestData
  Scenario: On voit bien toute la page, même le footer
    Given I am on "/event/forum/tickets"
    Then I should see "Billetterie: forum"
    And I should see "Si vous rencontrez le moindre problème, n'hésitez pas à nous contacter à l'adresse bonjour [@] afup.org."
    Then The "purchase[tickets][0][civility]" field should only contain the follow values '["M.", "Mme"]'
    When I fill in "purchase[tickets][0][firstname]" with "Prénom personne 1"
    And I fill in "purchase[tickets][0][lastname]" with "Nom personne 1"
    And I fill in "purchase[tickets][0][email]" with "personne1@yahoo.fr"
    And I fill in "purchase[firstname]" with "Prénom facturation"
    And I fill in "purchase[lastname]" with "Nom facturation"
    And I fill in "purchase[address]" with "42 rue de Strasbourg"
    And I fill in "purchase[zipcode]" with "75003"
    And I fill in "purchase[city]" with "PARIS"
    And I fill in "purchase[email]" with "facturationEntreprise@yahoo.fr"
    And I check "purchase[cgv]"
    And I select "2" from "purchase[tickets][0][ticketEventType]"
    And I press "Confirmer & Payer"
    Then I should see "Paiement de vos billets"
    And I should see "Nous avons bien reçu votre inscription et nous vous en remercions !"
    And I should see "Vous serez redirigé vers notre partenaire paybox afin de procéder au paiement d'un montant de 250€"
