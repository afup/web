Feature: Administration - Partie Assemblée Générale

  Scenario: Un membre ne peut pas accéder à la gestion de l'assemblée générale
    Given I am logged-in with the user "paul" and the password "paul"
    And I am on "/admin/members/general_meeting"
    Then the response status code should be 403

  Scenario: Un membre ne peut pas accéder à la préparation de l'assemblée générale
    Given I am logged-in with the user "paul" and the password "paul"
    And I am on "/admin/members/general_meeting/prepare"
    Then the response status code should be 403

  @reloadDbWithTestData
  Scenario: Créer une assemblée générale
    Given I am logged in as admin and on the Administration
    And I follow "afup-main-menu-item--assemblee_generale_infos"
    Then the ".content h2" element should contain "Assemblée générale"
    When I follow "Préparer une assemblée générale"
    Then I should see "Préparer une assemblée générale"
    And I select "1" from "prepare_form_date_day"
    And I select "2" from "prepare_form_date_month"
    And I select "2023" from "prepare_form_date_year"
    And I fill in "prepare_form_description" with "Une super assemblée"
    And I press "Preparer"
    Then I should not see "Une erreur est survenue lors de la préparation des personnes physiques"
    And I should see "La préparation des personnes physiques a été ajoutée"

  Scenario: Modifier une assemblée générale
    Given I am logged in as admin and on the Administration
    And I follow "afup-main-menu-item--assemblee_generale_infos"
    Then the ".content h2" element should contain "Assemblée générale"
    When I follow "Modifier la description"
    Then I should see "Modifier l'assemblée générale"
    And I fill in "prepare_form[description]" with "Une super assemblée modifiée"
    And I press "Enregistrer"
    Then I should see "Description enregistrée"
