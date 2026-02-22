Feature: Gestion des Super Apéro

  @reloadDbWithTestData
  Scenario: Ajout d'un Super Apéro
    Given the current date is "2025-01-15 10:00:00"
    And I am logged in as admin and on the Administration
    And I am on "/admin/super-apero/"
    Then I should see "Liste des Super Apéros"
    When I follow "Planifier le Super Apéro 2025"
    Then I should see "Ajouter un Super Apéro"
    When I fill in "super_apero[date]" with "2025-03-11"
    And I fill in "super_apero[meetups][lyon][meetupId]" with "12345"
    And I fill in "super_apero[meetups][paris][description]" with "Super Apéro PHP à Paris"
    And I fill in "super_apero[meetups][bordeaux][meetupId]" with "67890"
    And I fill in "super_apero[meetups][bordeaux][description]" with "Super Apéro PHP à Bordeaux"
    And I press "Ajouter"
    Then I should see "Le Super Apéro 2025 a été ajouté"
    And I should see "2025"
    And I should see "11/03/2025"
    And I should see "lyon"
    And I should see "paris"
    And I should see "bordeaux"
    And I should see a green label "Actif"

  Scenario: Activation d'un Super Apéro
    Given the current date is "2025-03-11 23:59:59"
    And I am logged in as admin and on the Administration
    And I am on "/admin/super-apero/"
    Then I should see "Liste des Super Apéros"
    And I should see "2025"
    And I should see "11/03/2025"
    And I should see a green label "Actif"
    When the current date is "2025-03-12 00:00:00"
    And I am on "/admin/super-apero/"
    Then I should see "Liste des Super Apéros"
    And I should see "2025"
    And I should see "11/03/2025"
    And I should see a grey label "Inactif"

  Scenario: Un seul Super Apéro possible par année
    Given the current date is "2025-01-15 10:00:00"
    And I am logged in as admin and on the Administration
    And I am on "/admin/super-apero/add"
    When I fill in "super_apero[date]" with "2025-09-20"
    And I press "Ajouter"
    Then I should see "Un Super Apéro existe déjà pour l'année 2025."

  Scenario: Modifier la date d'un Super Apéro
    Given the current date is "2025-01-15 10:00:00"
    And I am logged in as admin and on the Administration
    And I am on "/admin/super-apero/"
    When I follow "modifier_1"
    Then I should see "Modifier le Super Apéro 2025"
    When I fill in "super_apero[date]" with "2025-06-15"
    And I press "Modifier"
    Then I should see "Le Super Apéro 2025 a été modifié"
    And I should see "2025"
    And I should see "15/06/2025"

  Scenario: Ajouter une ville à un Super Apéro
    Given the current date is "2025-01-15 10:00:00"
    And I am logged in as admin and on the Administration
    And I am on "/admin/super-apero/edit/1"
    When I fill in "super_apero[meetups][nantes][meetupId]" with "11111"
    And I fill in "super_apero[meetups][nantes][description]" with "Super Apéro PHP à Nantes"
    And I press "Modifier"
    Then I should see "Le Super Apéro 2025 a été modifié"
    And I should see "nantes"

  Scenario: Modifier une ville d'un Super Apéro
    Given the current date is "2025-01-15 10:00:00"
    And I am logged in as admin and on the Administration
    And I am on "/admin/super-apero/edit/1"
    When I fill in "super_apero[meetups][lyon][meetupId]" with "99999"
    And I fill in "super_apero[meetups][lyon][description]" with "Super Apéro PHP à Lyon modifié"
    And I fill in "super_apero[meetups][paris][description]" with "Super Apéro PHP à Paris modifié"
    And I fill in "super_apero[meetups][bordeaux][meetupId]" with "88888"
    And I fill in "super_apero[meetups][bordeaux][description]" with "Super Apéro PHP à Bordeaux modifié"
    And I press "Modifier"
    Then I should see "Le Super Apéro 2025 a été modifié"

  Scenario: Supprimer une ville d'un Super Apéro
    Given the current date is "2025-01-15 10:00:00"
    And I am logged in as admin and on the Administration
    And I am on "/admin/super-apero/edit/1"
    When I fill in "super_apero[meetups][nantes][meetupId]" with ""
    And I fill in "super_apero[meetups][nantes][description]" with ""
    And I press "Modifier"
    Then I should see "Le Super Apéro 2025 a été modifié"
    And I should not see "nantes"

  Scenario: Supprimer un Super Apéro
    Given the current date is "2025-01-15 10:00:00"
    And I am logged in as admin and on the Administration
    And I am on "/admin/super-apero/"
    When I press "supprimer_1"
    Then I should see "Le Super Apéro 2025 a été supprimé"
    And I should not see "15/06/2025"
