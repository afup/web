Feature: Administration - Partie Infos Speakers

  @reloadDbWithTestData

  Scenario: Un membre ne peut pas accéder aux infos Speakers
    Given I am logged-in with the user "paul" and the password "paul"
    And I am on "/admin/event/speakers-expenses"
    Then the response status code should be 403

  Scenario: Liste des speakers liés à un évènement et modification d'une information sur la page info speaker
    Given I am logged in as admin and on the Administration
    And I follow "afup-main-menu-item--admin_event_speakers_management"
    Then the ".content h2" element should contain "Infos speakers"
    And the ".content table" element should contain "Geoffrey BACHELET"
    When I follow the button of tooltip "Voir sa page"
    Then I should see "Ma page speaker : forum"
    And I should see "Nous vous hébergeons"
    # Changement du numéro de téléphone
    And I should see "Moyen de contact"
    When I fill in "speakers_contact[phone_number]" with "0606060607"
    And I press "speakers_contact[submit]"
    Then I should see "Informations de contact enregistrées"
    Then the "speakers_contact_phone_number" field should contain "0606060607"
    # Restaurant
    And I should see "Nous vous invitons au restaurant"
    And I select "1" from "speakers_diner[will_attend]"
    And I select "1" from "speakers_diner[has_special_diet]"
    And I fill in "speakers_diner[special_diet_description]" with "Régime à base de fromages et de charcuteries."
    And I press "speakers_diner[submit]"
    Then I should see "Informations sur votre venue au restaurant des speakers enregistrées"
    Then the "speakers_diner[special_diet_description]" field should contain "Régime à base de fromages et de charcuteries."
    # Hébergement
    And I should see "Nous vous hébergeons"
    And I check "hotel_reservation_nights_0"
    And I check "hotel_reservation_nights_1"
    And I press "hotel_reservation[submit]"
    Then I should see "Informations sur votre venue à l'hôtel enregistrées"
    Then the "hotel_reservation_nights_0" checkbox should be checked
    Then the "hotel_reservation_nights_1" checkbox should be checked
    # Remboursement
    And I should see "Nous vous défrayons"
    And I attach the file "test_file1.pdf" to "speakers_expenses[files][]"
    And I press "speakers_expenses[submit]"
    Then I should see "Fichiers ajoutés"
    Then I should see "test_file1.pdf"
