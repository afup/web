Feature: Administration - Évènements - Inscriptions

  @reloadDbWithTestData
  Scenario: Accès à la liste puis ajout d'une inscription
    Given I am logged in as admin and on the Administration
    And I follow "Inscriptions"
    Then the ".content h2" element should contain "Inscriptions"
    When I follow "Ajouter"
    Then I should see "Ajouter une inscription pour le forum"
    Then The "civilite" field should only contain the follow values '["M.", "Mme"]'
    When I select "2" from "type_inscription"
    And I fill in "nom" with "Nom participant"
    And I fill in "prenom" with "Prénom participant"
    And I fill in "email" with "nomparticipant@gmail.com"
    And I select "2" from "type_reglement"
    And I press "Soumettre"
    Then I should see "L'inscription a été ajoutée"
    And I should see "Inscriptions"
    And I should see "Prénom participant Nom participant"

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
    And I follow "Gestion évènements"
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
    And I follow "Gestion évènements"
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
