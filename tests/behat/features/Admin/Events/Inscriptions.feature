Feature: Administration - Évènements - Inscriptions

  @reloadDbWithTestData
  Scenario: Statistiques des inscriptions
    Given I am logged in as admin and on the Administration
    And I follow "Inscriptions"
    Then the ".content h2" element should contain "Inscriptions"
    And I should see "Type Tarif Nb. inscrits Nb. confirmés Nb. payants Montant Places restantes"
    And I should see "2 Jours 250 € 3 3 3 750 € 494"
    And I should see "2 Jours AFUP 15 € 3 3 3 45 € 494"
    And I should see "Inscrits Confirmés En attente de règlement"
    And I should see "Jour 1 7 7 0"
    And I should see "Jour 2 7 7 0"

  @reloadDbWithTestData
  Scenario: Accès à la liste des inscriptions
    Given I am logged in as admin and on the Administration
    And I follow "Inscriptions"
    Then the ".content h2" element should contain "Inscriptions"
    And I should see "Michu Bernadette Helios Aerospace 2 Jours Réglé CB n/a"
    And I should see "Personne Paul 2 Jours AFUP Réglé CB OK"
    And I should see "Maurice Jean 2 Jours AFUP Réglé CB Expiré le 08/07/2019 URL Paiement"
    And I should see "Annulé Lepaiement 2 Jours Annulé CB n/a"
    And I should see "Super Bénévole AFUP Organisation Invité n/a"
    And I should see "Kirk James Tiberius 2 Jours Réglé VIR n/a"
    And I should see "Sans Cotisation 2 Jours AFUP Réglé CB Non trouvée"

  @reloadDbWithTestData
  Scenario: Filtrer la la liste des inscriptions
    Given I am logged in as admin and on the Administration
    And I follow "Inscriptions"
    Then the ".content h2" element should contain "Inscriptions"
    When I fill in "filter" with "Personne"
    And I press "Filtrer"
    And I should not see "Michu Bernadette Helios Aerospace 2 Jours Réglé CB n/a"
    And I should see "Personne Paul 2 Jours AFUP Réglé CB OK"
    And I should not see "Maurice Jean 2 Jours AFUP Réglé CB Expiré le 08/07/2019 URL Paiement"
    And I should not see "Annulé Lepaiement 2 Jours Annulé CB n/a"
    And I should not see "Kirk James Tiberius 2 Jours Réglé VIR n/a"
    And I should not see "Sans Cotisation 2 Jours AFUP Réglé CB Non trouvée"

  @reloadDbWithTestData
  Scenario: Ajout d'une inscription
    Given I am logged in as admin and on the Administration
    And I follow "Inscriptions"
    Then the ".content h2" element should contain "Inscriptions"
    When I follow "Ajouter"
    Then I should see "Ajouter une inscription pour le forum"
    Then The "ticket_admin_with_invoice[ticket][civility]" field should only contain the follow values '["M.", "Mme"]'
    When I select "2" from "ticket_admin_with_invoice[ticket][ticketTypeId]"
    And I fill in "ticket_admin_with_invoice[ticket][lastname]" with "Nom participant"
    And I fill in "ticket_admin_with_invoice[ticket][firstname]" with "Prénom participant"
    And I fill in "ticket_admin_with_invoice[ticket][email]" with "nomparticipant@gmail.com"
    And I select "2" from "ticket_admin_with_invoice[invoice][paymentType]"
    And I select "2" from "ticket_admin_with_invoice[ticket][status]"
    And I select "2" from "ticket_admin_with_invoice[ticket][invoiceStatus]"
    And I fill in "ticket_admin_with_invoice[invoice][lastname]" with "Nom facturation"
    And I fill in "ticket_admin_with_invoice[invoice][firstname]" with "Prénom facturation"
    And I fill in "ticket_admin_with_invoice[invoice][address]" with "Adresse facturation"
    And I fill in "ticket_admin_with_invoice[invoice][zipcode]" with "56532"
    And I fill in "ticket_admin_with_invoice[invoice][city]" with "ville facturation"
    And I fill in "ticket_admin_with_invoice[invoice][email]" with "nomparticipant@gmail.com"
    And I press "Soumettre"
    Then I should see "L'inscription a été ajoutée"
    And I should see "Inscriptions"
    And I should see "Nom participant Prénom participant"

# Bug sur cet export à reprendre une fois celui-ci corrigé
#  Scenario: Export CSV: Inscription
#    Given I am logged in as admin and on the Administration
#    # Export
#    And I follow "Inscriptions"
#    Then I should see "Exports"
#    And I follow "Exporter les inscriptions"
#    Then the response status code should be 200

  Scenario: Export CSV: Badges
    Given I am logged in as admin and on the Administration
    # Création d'un évènement
    And I follow "afup-main-menu-item--forum_gestion"
    Then the ".content h2" element should contain "Liste des évènements"
    When I follow "Ajouter"
    Then I fill in "event[title]" with "AFUP export badges"
    And I fill in "event[path]" with "afup-export-badges"
    And I fill in "event[seats]" with "666"
    And I fill in "event[placeName]" with "Paris"
    And I fill in "event[dateStart]" with "1970-01-01"
    And I fill in "event[dateEnd]" with "1970-01-01"
    And I press "Soumettre"
    # Export
    And I follow "Inscriptions"
    Then I should see "Exports"
    And I follow "Exporter les inscriptions pour les badges"
    Then the response status code should be 200
    And the response header "Content-disposition" should match '#^attachment; filename=inscriptions_forum_.*.csv#'

  Scenario: Export CSV: Inscrits aux 4 derniers évènements
    Given I am logged in as admin and on the Administration
    # Création d'un évènement
    And I follow "afup-main-menu-item--forum_gestion"
    Then the ".content h2" element should contain "Liste des évènements"
    When I follow "Ajouter"
    Then I fill in "event[title]" with "AFUP export derniers"
    And I fill in "event[path]" with "afup-export-derniers"
    And I fill in "event[seats]" with "4242"
    And I fill in "event[placeName]" with "Paris"
    And I fill in "event[dateStart]" with "1970-01-01"
    And I fill in "event[dateEnd]" with "1970-01-01"
    And I press "Soumettre"
    # Export
    And I follow "Inscriptions"
    Then I should see "Exports"
    And I follow "Exporter les inscrits aux 4 derniers évènements"
    Then the response status code should be 200
    And the response header "Content-disposition" should match '#^attachment; filename=inscriptions_([0-9]*)_derniers_events.csv#'
