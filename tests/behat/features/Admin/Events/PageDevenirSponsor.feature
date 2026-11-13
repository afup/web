Feature: Administration - Évènements - Page devenir sponsor

  Scenario: Un membre ne peut pas accéder à la page devenir sponsor
    Given I am logged-in with the user "paul" and the password "paul"
    And I am on "/admin/event/become-sponsor"
    Then the response status code should be 403

  @reloadDbWithTestData
  Scenario: On modifie les textes et les dossiers de la page sans évènement
    Given I am logged in as admin and on the Administration
    And I follow "Page devenir sponsor"
    Then the ".content h2" element should contain "Page devenir sponsor"
    When I fill in "sponsorship_settings[textFr]" with "Texte de sponsoring en français"
    And I fill in "sponsorship_settings[textEn]" with "Sponsoring text in english"
    And I attach the file "test_file1.pdf" to "sponsorship_settings[sponsor_file_fr]"
    And I attach the file "test_file1.pdf" to "sponsorship_settings[sponsor_file_en]"
    And I press "Enregistrer"
    Then I should see "La page devenir sponsor a été modifiée"
    And I should see "Voir le dossier de sponsoring (FR)"
    And I should see "Voir le dossier de sponsoring (EN)"
    When I go to "/become-sponsor"
    Then I should see "Texte de sponsoring en français"
    And I should see "Recevoir notre dossier"

  @reloadDbWithTestData
  Scenario: En mode évènement, la page redirige vers la page de sponsoring du dernier évènement
    Given I am logged in as admin and on the Administration
    And I go to "/admin/event/become-sponsor"
    When I select "event" from "sponsorship_settings[mode]"
    And I press "Enregistrer"
    Then I should see "La page devenir sponsor a été modifiée"
    When I go to "/become-sponsor"
    Then the url should match "/event/forum/sponsor/become-sponsor"
    And the "h1" element should contain "Devenir sponsor"
