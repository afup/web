Feature: Site Public - Super Apéro PHP

  @reloadDbWithTestData
  Scenario: Page du Super Apéro désactivée si aucun Super Apéro pour l'année courante
    Given I am on "/super-apero"
    Then the current URL should match "#/home#"

  @reloadDbWithTestData
  Scenario: Page du Super Apéro désactivée si le Super Apéro de l'année est passé
    Given the current date is "2025-02-12 00:00:00"
    # Ajout d'un Super Apéro
    And I am logged in as admin and on the Administration
    And I am on "/admin/super-apero/"
    Then I should see "Liste des Super Apéros"
    When I follow "Planifier le Super Apéro 2025"
    Then I should see "Ajouter un Super Apéro"
    When I fill in "super_apero[date]" with "2025-02-11"
    And I fill in "super_apero[meetups][lyon][meetupId]" with "12345"
    And I press "Ajouter"
    # Affichage de la page du Super Apéro
    Given I am on "/super-apero"
    Then the current URL should match "#/home#"

  @reloadDbWithTestData
  Scenario: Page du Super Apéro active si un Super Apéro est actif dans l'année courante
    Given the current date is "2025-03-11 23:59:59"
    # Ajout d'un Super Apéro
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
    # Affichage de la page du Super Apéro
    Given I am on "/super-apero"
    Then the current URL should match "#/association/super-apero$#"
    And the "#main h1" element should contain "Super-apéro PHP"
    And the response should contain "Super Apéro PHP à Paris"
    And the response should contain "Super Apéro PHP à Bordeaux"
