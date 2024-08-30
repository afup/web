Feature: Event pages - Ticketing

  @reloadDbWithTestData
  @clearEmails
  Scenario: Achat de billet CB
    Given I am on "/event/forum/tickets"
    Then I should see "Billetterie: forum"
    And I should see "Si vous rencontrez le moindre problème, n'hésitez pas à nous contacter à l'adresse bonjour [@] afup.org."
    # Choix du règlement par CB
    And I select "0" from "purchase[paymentType]"
    Then The "purchase[tickets][0][civility]" field should only contain the follow values '["M.", "Mme"]'
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
    # Formule Deuxième journée
    And I select "2" from "purchase[tickets][0][ticketEventType]"
    And I press "Confirmer & Payer"
    Then I should see "Paiement de vos billets"
    And I should see "Nous avons bien reçu votre inscription et nous vous en remercions !"
    And I should see "Vous serez redirigé vers notre partenaire paybox afin de procéder au paiement d'un montant de 250€"
    # Bascule vers Paybox
    When I press "Régler par carte"
    # Pour suivre la redirection POST de Paybox
    And I submit the form with name "PAYBOX"
    When I fill in "NUMERO_CARTE" with "1111222233334444"
    And I select "12" from "MOIS_VALIDITE"
    And I select "25" from "AN_VALIDITE"
    And I fill in "CVVX" with "123"
    And I press "Valider"
    Then I should see "PAIEMENT ACCEPTÉ"
    When I follow "Retour"
    Then I should see "Le paiement de votre commande s'est bien passé, merci."
    # Simuler l'appel de callback Paybox
    And simulate the Paybox callback
    And I should only receive the following emails:
      | to                                                    | subject                |
      | <facturationEntreprise@yahoo.fr>,<tresorier@afup.org> | Facture évènement AFUP |
      | <personne1@yahoo.fr>                                  | [forum] Merci !        |

  @reloadDbWithTestData
  Scenario: Achat de billet virement
    Given I am on "/event/forum/tickets"
    Then I should see "Billetterie: forum"
    And I should see "Si vous rencontrez le moindre problème, n'hésitez pas à nous contacter à l'adresse bonjour [@] afup.org."
    # Choix du règlement par virement
    And I select "2" from "purchase[paymentType]"
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
    # Formule Deuxième journée
    And I select "2" from "purchase[tickets][0][ticketEventType]"
    When I press "Confirmer & Payer"
    Then I should see "Paiement de vos billets"
    And I should see "Nous avons bien reçu votre inscription et nous vous en remercions !"
    And I should see "Pour les paiements par virement, merci d'envoyer le virement d'un montant de 250€ aux coordonnées suivantes :"
    And I should see "Pensez à indiquer dans le libellé du virement la référence suivante:"

  @reloadDbWithTestData
  Scenario: Achat de billet tarif membre
    Given I am logged-in with the user "paul" and the password "paul"
    And I am on "/event/forum/tickets"
    Then I should see "Billetterie: forum"
    # Formule Tarif membre
    And I select "0" from "purchase[tickets][0][ticketEventType]"
    Then The "purchase[tickets][0][civility]" field should only contain the follow values '["M.", "Mme"]'
    When I fill in "purchase[tickets][0][firstname]" with "Prénom personne 1"
    And I fill in "purchase[tickets][0][lastname]" with "Nom personne 1"
    And I fill in "purchase[tickets][0][email]" with "paul.personne@mycorp.fr"
    And I fill in "purchase[firstname]" with "Prénom facturation"
    And I fill in "purchase[lastname]" with "Nom facturation"
    And I fill in "purchase[address]" with "42 rue de Strasbourg"
    And I fill in "purchase[zipcode]" with "75003"
    And I fill in "purchase[city]" with "PARIS"
    And I fill in "purchase[email]" with "facturationEntreprise@yahoo.fr"
    And I check "purchase[cgv]"
    When I press "Confirmer & Payer"
    Then I should see "Paiement de vos billets"
    And I should see "Vous serez redirigé vers notre partenaire paybox afin de procéder au paiement d'un montant de 15€"
