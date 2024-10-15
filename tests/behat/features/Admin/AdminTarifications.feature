Feature: Administration - Partie Tarifications

  Scenario: Un membre ne peut pas accéder à la tarification
    Given I am logged-in with the user "paul" and the password "paul"
    And I am on "/admin/event/prices"
    Then the response status code should be 403

  @reloadDbWithTestData
  Scenario: Ajout d'une tarification
    Given I am logged in as admin and on the Administration
    And I follow "Tarifications"
    Then the ".content h2" element should contain "Tarifications"
    When I follow "Ajouter"
    Then I should see "Tarifications - Ajouter"
    Then I select "3" from "ticket_event_ticketType"
    And I fill in "ticket_event_price" with "42"
    # StartDate
    And I select "20" from "ticket_event_dateStart_date_day"
    And I select "10" from "ticket_event_dateStart_date_month"
    And I select "2021" from "ticket_event_dateStart_date_year"
    And I select "09" from "ticket_event_dateStart_time_hour"
    And I select "00" from "ticket_event_dateStart_time_minute"
    # EndDate
    And I select "21" from "ticket_event_dateEnd_date_day"
    And I select "10" from "ticket_event_dateEnd_date_month"
    And I select "2021" from "ticket_event_dateEnd_date_year"
    And I select "18" from "ticket_event_dateEnd_time_hour"
    And I select "00" from "ticket_event_dateEnd_time_minute"
    And I fill in "ticket_event_description" with "Une description"
    And I fill in "ticket_event_maxTickets" with "12"
    And I press "Ajouter"
    Then I should see "Le tarif a été ajouté"
    And I should see "Tarifications"
    And I should see "AFUP_FORUM_2_JOURNEES_AFUP"
    And I should see "20/10/2021 09:00"
    And I should see "21/10/2021 18:00"

  Scenario: Modification d'une tarification
    Given I am logged in as admin and on the Administration
    And I follow "Tarifications"
    Then the ".content h2" element should contain "Tarifications"
    When I follow "Modifier le tarif 2 Jours AFUP"
    Then I should see "AFUP_FORUM_2_JOURNEES_AFUP - 2 Jours AFUP - (réservé aux membres) - JOUR 1, JOUR 2"
    And I fill in "ticket_event_price" with "142"
    # StartDate
    And I select "20" from "ticket_event_dateStart_date_day"
    And I select "11" from "ticket_event_dateStart_date_month"
    And I select "2021" from "ticket_event_dateStart_date_year"
    And I select "09" from "ticket_event_dateStart_time_hour"
    And I select "00" from "ticket_event_dateStart_time_minute"
    # EndDate
    And I select "21" from "ticket_event_dateEnd_date_day"
    And I select "11" from "ticket_event_dateEnd_date_month"
    And I select "2021" from "ticket_event_dateEnd_date_year"
    And I select "18" from "ticket_event_dateEnd_time_hour"
    And I select "00" from "ticket_event_dateEnd_time_minute"
    And I fill in "ticket_event_description" with "Une description 2"
    And I fill in "ticket_event_maxTickets" with "120"
    And I press "Modifier"
    Then I should see "Le tarif a été modifié"
    And I should see "Tarifications"
    And I should see "AFUP_FORUM_2_JOURNEES_AFUP"
    And I should see "20/11/2021 09:00"
    And I should see "21/11/2021 18:00"
