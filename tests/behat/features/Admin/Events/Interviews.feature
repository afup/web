Feature: Administration - Évènements - Interviews

  Background:
    Given I am logged in as admin and on the Administration
    And I follow "afup-main-menu-item--admin_event_interview_list"
    Then the ".content h2" element should contain "Interviews"
    And I follow "Configurer"
    When I select "1" from "interview_config[interviewsWordpressCategoryId]"
    And I fill in "interview_config[interviewsIntro]" with "Les super interviews"
    And I fill in "interview_config[interviewsCtaText]" with "Le bouton"
    And I press "Enregistrer"

  @reloadDbWithTestData
  Scenario: Lister les speakers planifiés sans interview
    Given I follow "afup-main-menu-item--admin_event_interview_list"
    Then I should not see "Configuration WordPress manquante"
    And I should see "Nouvelle interview"
    And I should see "Aucune interview pour le moment"
    And I should see "Speakers sans interview"
    And I should see "Geoffrey BACHELET"
    And I should see "Adrien GALLOU"

  @reloadDbWithTestData
  Scenario: Création d'une interview pour un speaker
    And I follow "afup-main-menu-item--admin_event_interview_list"
    And I am on "/admin/event/interviews/add"
    Then the ".content h2" element should contain "Interview"
    When I select "Geoffrey BACHELET" from "interview[speakers][]"
    And I fill in "interview[datePublication]" with "2026-06-01T10:00:00"
    And I fill in "interview[questions][0][question]" with "Quelle est la différence entre un pigeon ?"
    And I fill in "interview[questions][0][reponse]" with "Aucune. Les deux pattes ont la même taille, surtout la gauche."
    And I press "Enregistrer"
    Then I should see "L'interview a été enregistrée"
    And the ".content table" element should contain "Geoffrey BACHELET"
    And I should see "01/06/2026"

    Given I am on "/blog/talk_widget?ids=1&type=interview"
    Then the response should contain "Quelle est la différence entre un pigeon ?"
    And the response should contain "Aucune. Les deux pattes ont la même taille, surtout la gauche."

  @reloadDbWithTestData
  Scenario: Création d'une interview groupant plusieurs speakers
    And I follow "afup-main-menu-item--admin_event_interview_list"
    And I follow "Nouvelle interview"
    And I fill in "interview[datePublication]" with "2026-06-01T10:00:00"
    When I select "Geoffrey BACHELET" from "interview[speakers][]"
    And I additionally select "Adrien GALLOU" from "interview[speakers][]"
    And I fill in "interview[questions][0][question]" with "Où est-elle ?"
    And I fill in "interview[questions][0][reponse]" with "Ici."
    And I press "Enregistrer"
    Then I should see "L'interview a été enregistrée"
    And the ".content table" element should contain "Adrien GALLOU, Geoffrey BACHELET"

  @reloadDbWithTestData
  Scenario: Acces refusé pour un membre simple
    Given I am logged-in with the user "paul" and the password "paul"
    And I am on "/admin/event/interviews"
    Then the response status code should be 403

  @reloadDbWithTestData
  Scenario: Interview inexistante
    Given I am logged in as admin and on the Administration
    And I am on "/admin/event/interviews/9999"
    Then the response status code should be 404
