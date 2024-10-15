Feature: Administration - Partie Speakers


  Scenario: Un membre ne peut pas accéder à la gestion des Speakers
    Given I am logged-in with the user "paul" and the password "paul"
    And I am on "/admin/event/speakers-management"
    Then the response status code should be 403

  @reloadDbWithTestData
  Scenario: Ajout/modification/suppression d'un speaker
    Given I am logged in as admin and on the Administration
    And I follow "Speakers"
    Then I should see "Créer les insciptions conférencier pour le forum"
    # ajout d'un speaker
    When I follow "Ajouter"
    Then I should see "Ajouter un conférencier pour le forum"
    Then The "speaker[github_user]" field should only contain the follow values '["","agallou (Adrien Gallou)","ubermuda (Geoffrey Bachelet)"]'
    When I fill in "speaker[firstname]" with "Damien"
    When I fill in "speaker[lastname]" with "Segui"
    When I fill in "speaker[email]" with "damien@gmail.com"
    When I fill in "speaker[phone_number]" with "0606060606"
    When I fill in "speaker[biography]" with "Bio Damien Seguy"
    And I press "Sauvegarder"
    Then I should see "Le conférencier a été ajouté"
    And I should see "Créer les insciptions conférencier pour le forum"
    And the ".content table" element should contain "Segui Damien"
    # modification d'un speaker
    When I follow the button of tooltip "Modifier la fiche de Segui Damien"
    Then I should see "Modifier un conférencier pour le forum"
    When I fill in "speaker[lastname]" with "Seguy"
    When I fill in "speaker[phone_number]" with "0606060607"
    And I press "Sauvegarder"
    Then I should see "Le conférencier a été modifié"
    And I should see "Créer les insciptions conférencier pour le forum"
    And the ".content table" element should contain "Seguy Damien"
    # association à un compte Github
    When I follow the button of tooltip "Modifier la fiche de Seguy Damien"
    Then I should see "Modifier un conférencier pour le forum"
    When I select "1" from "speaker[github_user]"
    And I press "Sauvegarder"
    Then I should see "Le conférencier a été modifié"
    # vérification association compte Github
    When I follow the button of tooltip "Modifier la fiche de Seguy Damien"
    Then I should see "Modifier un conférencier pour le forum"
    And The "speaker[github_user]" field should has the following selected value "1"
    When I press "Sauvegarder"
    Then I should see "Le conférencier a été modifié"
    # suppression d'un speaker
    When I follow the button of tooltip "Supprimer la fiche de Seguy Damien"
    Then I should see "Créer les insciptions conférencier pour le forum"
    And I should see "Le conférencier a été supprimé"
    But the ".content table" element should not contain "Seguy Damien"
