Feature: Event pages - Ticketing (JavaScript)

  @reloadDbWithTestData
  @javascript
  Scenario: Parcours d'achat en trois étapes pour deux billets
    Given I am on "/event/forum/tickets"
    And I clear the local storage
    # Étape 1 : nombre de billets
    Then I should see "Combien de billets souhaitez-vous commander ?"
    And I should not see "Informations sur les participant•e•s"
    When I select "2" from "purchase[nbPersonnes]"
    And I press the visible "Suivant" button
    # Étape 2 : une section par billet, avec le premier tarif disponible présélectionné
    Then I wait until I see "Informations sur les participant•e•s"
    And I should not see "Combien de billets souhaitez-vous commander ?"
    And I should see "Billet 2"
    And I should not see "Billet 3"
    And the "#ticket-summary table" element should contain "<td>Deuxième journée</td><td class=\"text-right\">136,36€</td><td class=\"text-right\">x2</td><td class=\"text-right\">272,73€</td><td class=\"text-right\">300,00€</td>"
    # Formule « 2 Jours » pour le second billet : le récapitulatif est recalculé
    When I select "2" from "purchase[tickets][1][ticketEventType]"
    Then the "#ticket-summary table" element should contain "<td>2 Jours</td><td class=\"text-right\">227,27€</td><td class=\"text-right\">x1</td><td class=\"text-right\">227,27€</td><td class=\"text-right\">250,00€</td>"
    And the "#ticket-summary table" element should contain "<td>Total</td><td></td><td class=\"text-right\">x2</td><td class=\"text-right\">363,64€</td><td class=\"text-right\">400,00€</td>"
    When I fill in "purchase[tickets][0][firstname]" with "Prénom personne 1"
    And I fill in "purchase[tickets][0][lastname]" with "Nom personne 1"
    And I fill in "purchase[tickets][0][email]" with "personne1@yahoo.fr"
    And I fill in "purchase[tickets][1][firstname]" with "Prénom personne 2"
    And I fill in "purchase[tickets][1][lastname]" with "Nom personne 2"
    And I fill in "purchase[tickets][1][email]" with "personne2@yahoo.fr"
    # Recopie des informations de transport du premier billet
    And I select "10" from "purchase[tickets][0][transportMode]"
    And I select "25" from "purchase[tickets][0][transportDistance]"
    And I press "Recopier les informations de transport pour toutes les personnes"
    Then The "purchase[tickets][1][transportMode]" field should have the following selected value "10"
    And The "purchase[tickets][1][transportDistance]" field should have the following selected value "25"
    When I press the visible "Suivant" button
    # Étape 3 : facturation préremplie avec le premier billet
    Then I wait until I see "Facturation et Paiement"
    And I should not see "Informations sur les participant•e•s"
    And the "purchase[firstname]" field should contain "Prénom personne 1"
    And the "purchase[lastname]" field should contain "Nom personne 1"
    And the "purchase[email]" field should contain "personne1@yahoo.fr"
    When I fill in "purchase[address]" with "42 rue de Strasbourg"
    And I fill in "purchase[zipcode]" with "75003"
    And I fill in "purchase[city]" with "PARIS"
    # Paiement par virement, dans les autres moyens de paiement
    And I press "Autres moyens de paiement"
    And I select "2" from "purchase[paymentType]"
    And I check "purchase[cgv]"
    And I press "Confirmer & Payer" and wait until I see "Paiement de vos billets"
    Then I should see "Pour les paiements par virement, merci d'envoyer le virement d'un montant de 400€ aux coordonnées suivantes :"

  @reloadDbWithTestData
  @javascript
  Scenario: Validation de chaque étape avant de passer à la suivante
    Given I am on "/event/forum/tickets"
    And I clear the local storage
    When I press the visible "Suivant" button
    And I wait until I see "Informations sur les participant•e•s"
    # Champs du billet vides
    And I press the visible "Suivant" button
    Then I should see the alert "Veuillez remplir tous les champs obligatoires pour le billet 1."
    And I should see "Informations sur les participant•e•s"
    And I should not see "Facturation et Paiement"
    When I fill in "purchase[tickets][0][firstname]" with "Prénom personne 1"
    And I fill in "purchase[tickets][0][lastname]" with "Nom personne 1"
    And I fill in "purchase[tickets][0][email]" with "personne1@yahoo.fr"
    And I press the visible "Suivant" button
    # Mode de transport non renseigné
    Then I should see the alert "Veuillez remplir tous les champs obligatoires pour le billet 1."
    When I select "10" from "purchase[tickets][0][transportMode]"
    And I select "25" from "purchase[tickets][0][transportDistance]"
    And I press the visible "Suivant" button
    Then I wait until I see "Facturation et Paiement"
    # Retour à l'étape précédente, sans perdre la saisie
    When I press the visible "Précédent" button
    Then I wait until I see "Informations sur les participant•e•s"
    And the "purchase[tickets][0][firstname]" field should contain "Prénom personne 1"
    When I press the visible "Suivant" button
    And I wait until I see "Facturation et Paiement"
    And I fill in "purchase[address]" with "42 rue de Strasbourg"
    And I fill in "purchase[zipcode]" with "75003"
    And I fill in "purchase[city]" with "PARIS"
    # CGV non acceptées : la commande n'est pas envoyée
    And I press "Confirmer & Payer"
    Then I should see "Facturation et Paiement"
    And I should not see "Paiement de vos billets"

  @reloadDbWithTestData
  @javascript
  Scenario: La saisie est conservée après un rechargement de la page
    Given I am on "/event/forum/tickets"
    And I clear the local storage
    When I select "2" from "purchase[nbPersonnes]"
    And I press the visible "Suivant" button
    And I wait until I see "Informations sur les participant•e•s"
    And I fill in "purchase[tickets][0][firstname]" with "Prénom personne 1"
    And I select "2" from "purchase[tickets][0][ticketEventType]"
    And I reload the page
    Then The "purchase[nbPersonnes]" field should have the following selected value "2"
    When I press the visible "Suivant" button
    Then I wait until I see "Informations sur les participant•e•s"
    And I should see "Billet 2"
    And the "purchase[tickets][0][firstname]" field should contain "Prénom personne 1"
    And the "#ticket-summary table" element should contain "<td>2 Jours</td>"

  @reloadDbWithTestData
  @javascript
  Scenario: Après une erreur côté serveur, l'étape en erreur est affichée
    Given I am on "/event/forum/tickets"
    And I clear the local storage
    When I press the visible "Suivant" button
    And I wait until I see "Informations sur les participant•e•s"
    And I fill in "purchase[tickets][0][firstname]" with "Prénom personne 1"
    And I fill in "purchase[tickets][0][lastname]" with "Nom personne 1"
    # Email accepté par le navigateur mais refusé par le serveur (pas de TLD)
    And I fill in "purchase[tickets][0][email]" with "personne1@hostname"
    And I select "10" from "purchase[tickets][0][transportMode]"
    And I select "25" from "purchase[tickets][0][transportDistance]"
    And I press the visible "Suivant" button
    And I wait until I see "Facturation et Paiement"
    And I fill in "purchase[address]" with "42 rue de Strasbourg"
    And I fill in "purchase[zipcode]" with "75003"
    And I fill in "purchase[city]" with "PARIS"
    And I check "purchase[cgv]"
    And I press "Confirmer & Payer" and wait until I see "Cette valeur n'est pas une adresse email valide."
    Then I wait until I see "Informations sur les participant•e•s"
    And I should not see "Facturation et Paiement"
