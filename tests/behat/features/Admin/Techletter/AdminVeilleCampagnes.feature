Feature: Administration - Veille - Campagnes

  @reloadDbWithTestData
  @test
  @clearEmails
  Scenario: Créer une campagne pour la veille tech
    Given I am logged in as admin and on the Administration
    And I follow "Campagnes"
    Then the ".content h2:nth-child(2)" element should contain "Campagnes"
    And I should see "ID" in the "thead > tr:first-child > th:nth-child(1)" element
    And I should see "Date d'envoi planifiée" in the "thead > tr:first-child > th:nth-child(2)" element
    And I should see "Envoyée à mailchimp ?" in the "thead > tr:first-child > th:nth-child(3)" element

    When I fill in "sending_sendingDate" with "2023-02-01"
    And I press "sending_save"
    Then the ".content h2" element should contain "Veille de l'AFUP du 01/02/2023"

    When I press "Envoyer une newsletter de test au Pôle"
    And I should only receive the following emails:
      | to                     | subject                               |
      | <pole-veille@afup.org> | [Test] Veille de l'AFUP du 01/02/2023 |
    And the ".content .header" element should contain "Le mail de test a été envoyé"

    When I fill in "sendingDate" with "2023-02-01 12:00:00"
    And I press "Mettre à jour la date de planification"
    And the ".content .header" element should contain "Date mise à jour"

    When I follow "Campagnes"
    Then the ".content h2:nth-child(2)" element should contain "Campagnes"
    And I should see "3" in the "tbody > tr:first-child > td:nth-child(1)" element
    And I should see "01/02/2023" in the "tbody > tr:first-child > td:nth-child(2)" element
    And I should see "non" in the "tbody > tr:first-child > td:nth-child(3)" element
