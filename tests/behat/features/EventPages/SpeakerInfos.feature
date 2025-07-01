Feature: Event > Profil speaker

  @reloadDbWithTestData
  Scenario: Accès à la page
    Given I go to "/event/forum/speaker-infos"
    When I follow "Connect as agallou"
    Then I should see "Votre conférence"
    Then I should see "Nous vous défrayons"

  @reloadDbWithTestData
  Scenario: Saisie des infos
    Given I go to "/event/forum/speaker-infos"
    When I follow "Connect as agallou"
    Then I fill in "speakers_contact[phone_number]" with "0123456789"
    And I press "Enregistrer le contact"
    Then I should see "Informations de contact enregistrées"

    Then I fill in "speakers_diner[will_attend]" with "1"
    Then I fill in "speakers_diner[has_special_diet]" with "1"
    Then I fill in "speakers_diner[special_diet_description]" with "Je suis végétarien"
    And I press "Enregistrer mes préférences pour le restaurant"
    Then I should see "Informations sur votre venue au restaurant des speakers enregistrées"
    And the "speakers_diner_will_attend_0" checkbox should be checked
    And the "speakers_diner_will_attend_1" checkbox should be unchecked
    And the "speakers_diner_has_special_diet_0" checkbox should be unchecked
    And the "speakers_diner_has_special_diet_1" checkbox should be checked
    And I should see "Je suis végétarien"

    When I check "hotel_reservation_nights_0"
    And I press "Enregistrer les nuitées"
    Then I should see "Informations sur votre venue à l'hôtel enregistrées"
    Then the "hotel_reservation_nights_0" checkbox should be checked

    When I attach the file "test_file2.pdf" to "speakers_expenses[files][]"
    And I press "Ajouter des fichiers"
    When I should see "Fichiers ajoutés"
    Then I should see "test_file2.pdf"

  @reloadDbWithTestData
  Scenario: Saisie du hosting sponsor
    Given I go to "/event/forum/speaker-infos"
    When I follow "Connect as agallou"

    When I check "hotel_reservation_nights_4"
    And I press "Enregistrer les nuitées"
    Then I should see "Informations sur votre venue à l'hôtel enregistrées"
    Then the "hotel_reservation_nights_3" checkbox should be checked
    Then the "hotel_reservation_nights_4" checkbox should be checked

  @reloadDbWithTestData
  Scenario: Saisie du travel - pas besoin
    Given I go to "/event/forum/speaker-infos"
    When I follow "Connect as agallou"

    When I check "travel_sponsor_choices_0"
    And I press "Enregistrer le travel sponsor"
    Then I should see "Informations sur vos transports enregistrées "
    Then the "travel_sponsor_choices_0" checkbox should be checked

  @reloadDbWithTestData
  Scenario: Saisie du travel - sponsorisé
    Given I go to "/event/forum/speaker-infos"
    When I follow "Connect as agallou"

    When I check "travel_sponsor_choices_1"
    And I press "Enregistrer le travel sponsor"
    Then I should see "Informations sur vos transports enregistrées "
    Then the "travel_sponsor_choices_1" checkbox should be checked
