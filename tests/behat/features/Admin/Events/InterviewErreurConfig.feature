Feature: Administration - Évènements - Interviews - Erreur de config

  @reloadDbWithTestData
  Scenario: La configuration WordPress est obligatoire
    Given I am logged in as admin and on the Administration
    And I follow "afup-main-menu-item--admin_event_interview_list"
    Then the ".content h2" element should contain "Interviews"
    And I should see "Configuration WordPress manquante"
