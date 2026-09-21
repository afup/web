Feature: Event pages - Billeterie privee

  @reloadDbWithTestData
  Scenario: Apres une commande sur une billetterie privee, le tarif prive n'est plus propose
    Given I am on "/event/forum/tickets/billeterie-privee/token-billetterie-privee-test"
    Then I should see "Billetterie privée"
    When I fill in "billeterie_privee_mot_de_passe" with "password"
    And I press "OK"
    Then I should see "Billetterie: forum"
    And I should see "80€"
    # Choix du règlement par CB
    And I select "0" from "purchase[paymentType]"
    # Formule billetterie privée (80€), seule formule proposée
    And I select "0" from "purchase[tickets][0][ticketEventType]"
    When I fill in "purchase[tickets][0][firstname]" with "Prénom personne 1"
    And I fill in "purchase[tickets][0][lastname]" with "Nom personne 1"
    And I fill in "purchase[tickets][0][email]" with "personne1@yahoo.fr"
    When I should see "Quel est votre mode de transport ?"
    And I select "10" from "purchase[tickets][0][transportMode]"
    And I select "25" from "purchase[tickets][0][transportDistance]"
    And I fill in "purchase[firstname]" with "Prénom facturation"
    And I fill in "purchase[lastname]" with "Nom facturation"
    And I fill in "purchase[address]" with "42 rue de Strasbourg"
    And I fill in "purchase[zipcode]" with "75003"
    And I fill in "purchase[city]" with "PARIS"
    And I fill in "purchase[email]" with "facturationEntreprise@yahoo.fr"
    And I check "purchase[cgv]"
    When I press "Confirmer & Payer"
    Then I should see "Paiement de vos billets"
    # Retour sur la billetterie publique : le tarif privé n'est plus proposé
    When I am on "/event/forum/tickets"
    Then I should not see "80€"
    # Il faut repasser par la billetterie privée et ressaisir le mot de passe
    When I am on "/event/forum/tickets/billeterie-privee/token-billetterie-privee-test"
    Then I should see "Mot de passe"
