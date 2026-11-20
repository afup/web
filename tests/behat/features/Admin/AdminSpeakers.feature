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
    Then The "speaker[github_user]" field should only contain the follow values '["","agallou (Adrien Gallou)","ancien (Marie Ancienne)","ubermuda (Geoffrey Bachelet)","UnLoginGithub (John Doe)"]'
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
    And The "speaker[github_user]" field should have the following selected value "1"
    When I press "Sauvegarder"
    Then I should see "Le conférencier a été modifié"
    # suppression d'un speaker
    When I follow the button of tooltip "Supprimer la fiche de Seguy Damien"
    Then I should see "Créer les insciptions conférencier pour le forum"
    And I should see "Le conférencier a été supprimé"
    But the ".content table" element should not contain "Seguy Damien"

  @reloadDbWithTestData
  Scenario: Reprise d'un conférencier d'une édition passée
    Given the speaker photo "avatar1.png" is stored as "3/originals/4.png"
    And I am logged in as admin and on the Administration
    And I follow "Speakers"
    When I follow "Reprendre un conférencier"
    Then I should see "Reprendre un conférencier pour le forum"
    When I fill in "q" with "ancienne"
    And I press "Rechercher"
    Then I should see "Un évènement du passé"
    And I should see "marie-ancienne"
    When I follow the button of tooltip "Reprendre Ancienne Marie"
    Then I should see "Ajouter un conférencier pour le forum"
    And I should see "Fiche reprise de Marie Ancienne"
    And the "speaker[firstname]" field should contain "Marie"
    And the "speaker[locality]" field should contain "Lyon"
    And The "speaker[github_user]" field should have the following selected value "4"
    When I fill in "speaker[referent_person_email]" with "referente@example.com"
    And I press "Sauvegarder"
    Then I should see "Le conférencier a été ajouté"
    And the ".content table" element should contain "Ancienne Marie"
    # même personne, champs et photo repris
    When I follow the button of tooltip "Modifier la fiche de Ancienne Marie"
    Then the "#speaker-personne" element should contain "marie-ancienne"
    And the "speaker[locality]" field should contain "Lyon"
    And the "speaker[referent_person_email]" field should contain "referente@example.com"
    And I should see "Voir la photo originale"
    # la reprendre une seconde fois mène à la fiche existante
    When I am on "/admin/speaker/pick?eventId=1&q=ancienne"
    And I follow the button of tooltip "Reprendre Ancienne Marie"
    Then I should see "Ce conférencier existe déjà pour cet événement"
    And I should see "Modifier un conférencier pour le forum"

  @reloadDbWithTestData
  Scenario: Rattacher, détacher et fusionner sur la fiche personne
    Given I am logged in as admin and on the Administration
    And I follow "Speakers"
    When I follow the button of tooltip "Modifier la fiche de Bachelet Geoffrey"
    Then the "#speaker-personne" element should contain "geoffrey-bachelet"
    When I follow "geoffrey-bachelet"
    Then I should see "Personne geoffrey-bachelet"
    And the "#personne-suggestions" element should contain "geoffrey@example.com"
    # rattacher la fiche homonyme d'une édition passée
    When I press "Rattacher" in the row of "geoffrey@example.com"
    Then I should see "Le conférencier a été rattaché à la personne"
    And the "#personne-speakers" element should contain "geoffrey@example.com"
    And I should see "Aucune suggestion."
    # la détacher
    When I press "Détacher" in the row of "geoffrey@example.com"
    Then I should see "Le conférencier a été détaché de la personne"
    And the "#personne-suggestions" element should contain "geoffrey@example.com"
    # lui créer sa propre personne depuis sa fiche, puis fusionner les deux personnes
    When I am on "/admin/speaker/edit?id=5"
    Then I should see "Cette fiche n'est rattachée à aucune personne."
    When I press "Créer la personne"
    Then I should see "La personne a été créée"
    And I should see "Personne geoffrey-bachelet-2"
    When I fill in "q" with "Bachelet"
    And I press "Rechercher"
    And I press "Fusionner" in the row of "geoffrey-bachelet"
    Then I should see "La personne geoffrey-bachelet a été fusionnée dans geoffrey-bachelet-2"
    And I should see 2 "#personne-speakers tbody tr" elements
    And the "#personne-speakers" element should contain "foo@bar.baz"
