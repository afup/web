Feature: Administration - Évènements - Conférences

  @reloadDbWithTestData
  Scenario: Accès à la liste des conférences
    Given I am logged in as admin and on the Administration
    And I follow "Conférences"
    Then the ".content h2" element should contain "Conférence"
    And I should see "3 CONFÉRENCE(S)"
    And the "table" element should contain "Soumission"
    And the "table" element should contain "Titre"
    And the "table" element should contain "Speaker(s)"
    And the "table" element should contain "Moy. notes"
    And the "table" element should contain "Nb. notes"
    And the "table" element should contain "Soumission"
    When I fill in "q" with "Jouons"
    And I press "Filtrer"
    Then I should see "1 CONFÉRENCE(S)"

  @reloadDbWithTestData
  Scenario: Ajout d'une conférence
    Given I am logged in as admin and on the Administration
    And I follow "Conférences"
    Then the ".content h2" element should contain "Conférence"
    And I follow "Ajouter"
    Then the ".content h2" element should contain "Ajouter une conférence pour le forum"
    Then I fill in "talk_admin[submittedOn]" with "2022-01-01 08:00:00"
    Then I fill in "talk_admin[title]" with "Une autre conference"
    Then I fill in "talk_admin[abstract]" with "Une conference cool"
    Then I fill in "talk_admin[type]" with "1"
    Then I fill in "talk_admin[skill]" with "3"
    Then I select "1" from "talk_admin[speakers][]"
    And I press "Soumettre"
    Then I should see "La conférence a été ajoutée"
    And I should see "4 CONFÉRENCE(S)"
    And I should see "01/01/2022"
    And I should see "Une autre conference"
    And I should see "Adrien Gallou"

  @reloadDbWithTestData
  Scenario: Modification d'une conférence
    Given I am logged in as admin and on the Administration
    And I follow "Conférences"
    Then the ".content h2" element should contain "Conférence"
    When I follow the button of tooltip "Modifier la conférence Jouons tous ensemble à un petit jeu"
    Then I fill in "talk_admin[submittedOn]" with "2022-12-31 10:00:00"
    Then I fill in "talk_admin[title]" with "Jouons à un jeu"
    Then I fill in "talk_admin[type]" with "3"
    Then I fill in "talk_admin[skill]" with "2"
    Then I select "1" from "talk_admin[speakers][]"
    And I press "Soumettre"
    Then I should see "La conférence a été modifiée"
    And I should see "3 CONFÉRENCE(S)"
    And I should see "31/12/2022"
    And I should see "Jouons à un jeu"
    And I should see "Adrien Gallou"

  @reloadDbWithTestData
  Scenario: Suppression d'une conférence
    Given I am logged in as admin and on the Administration
    And I follow "Conférences"
    Then the ".content h2" element should contain "Conférence"
    And I should see "3 CONFÉRENCE(S)"
    When I follow the button of tooltip "Supprimer la conférence Jouons tous ensemble à un petit jeu"
    Then I should see "La conférence a été supprimée"
    And I should not see "Jouons tous ensemble à un petit jeu"
    And I should see "2 CONFÉRENCE(S)"

  @reloadDbWithTestData
  Scenario: Exporter les conférences
    Given I am logged in as admin and on the Administration
    And I follow "Conférences"
    Then the ".content h2" element should contain "Conférence"
    And I follow "Exporter les conférences"
    Then the response status code should be 200
    And the response header "Content-disposition" should match '#^attachment; filename=talks_forum_([0-9]*)-([0-9]*).csv#'
    And the downloaded file should be the same as "admin_talks_export.csv"

  @reloadDbWithTestData
  Scenario: Export joindIn
    Given I am logged in as admin and on the Administration
    And I go to "/admin/talk/?id=3"
    Then the ".content h2" element should contain "Conférence"
    And I follow "Export joindIn"
    Then the response status code should be 200
    And the response header "Content-disposition" should match '#^attachment; filename=talks_passed_([0-9]*)-([0-9]*)_joind_in.csv#'
    And the downloaded file should be the same as "admin_talks_export_joind_in.csv"