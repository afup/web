Feature: Administration - Partie Tarifications

  @reloadDbWithTestData
  Scenario: Accès à la liste des tarifications
    Given I am logged in as admin and on the Administration
    And I follow "Tarifications"
    Then the ".content h2" element should contain "Tarifications"
    When I follow "Ajouter"
    Then I should see "Tarifications - Ajouter"
    Then I select "2" from "ticket_event_ticketType"
    And I fill in "ticket_event_price" with "42"
    And I fill in "ticket_event_dateStart" with "2021-10-20T09:00:00"
    And I fill in "ticket_event_dateEnd" with "2021-10-21T18:00:00"
    And I fill in "ticket_event_description" with "Une description"
    And I fill in "ticket_event_maxTickets" with "12"
    And I press "Ajouter"
    Then I should see "Le tarif a été ajouté"
    And I should see "Tarifications"
    And I should see "AFUP_FORUM_2_JOURNEES_AFUP"
    And I should see "20/10/2021 09:00"
    And I should see "21/10/2021 18:00"
