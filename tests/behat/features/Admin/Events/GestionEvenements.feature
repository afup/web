Feature: Administration - Évènements - Gestions Évènements

  Scenario: Un membre ne peut pas accéder à la liste des événements
    Given I am logged-in with the user "paul" and the password "paul"
    And I am on "/admin/event/list"
    Then the response status code should be 403

  @reloadDbWithTestData
  @clearEmails
  @clearAllMailInscriptionAttachments
  @clearAllSponsorFiles
  Scenario: On crée un nouvel évènement vide
    Given I am logged in as admin and on the Administration
    And I follow "Gestion évènements"
    Then the ".content h2" element should contain "Liste des évènements"
    When I follow "Ajouter"
    Then I should see "Ajouter un évènement"
    Then I should see "Informations générales"
    And I press "Soumettre"
    Then I should see "Titre du forum manquant"
    And I should see "Nombre de places manquant"

  Scenario: On affiche un évènement
    Given I am logged in as admin and on the Administration
    And I follow "Gestion évènements"
    Then the ".content h2" element should contain "Liste des évènements"
    And I follow the button of tooltip "Modifier le forum forum"
    Then the "event[title]" field should contain "forum"
    And the "event[path]" field should contain "forum"
    And the "event[logoUrl]" field should contain "http://78.media.tumblr.com/tumblr_lgkqc0mz9d1qfyzelo1_1280.jpg"
    And the "event[seats]" field should contain "500"
    And the "event[placeName]" field should contain "Paris"
    And the "event[placeAddress]" field should contain "Marriott Rive Gauche"
    And the "event[CFP][fr]" field should contain "François le français"
    And the "event[CFP][en]" field should contain "Henri l'anglais"
    And the "event[CFP][sponsor_management_fr]" field should contain "**Sponsors**, venez, vous serez très visible !"
    And the "event[CFP][sponsor_management_en]" field should contain "**Sponsors**, come, you will be very visible!"
    And the "event[CFP][mail_inscription_content]" field should contain "Contenu email"

  Scenario: On crée un nouvel évènement
    Given I am logged in as admin and on the Administration
    And I follow "Gestion évènements"
    Then the ".content h2" element should contain "Liste des évènements"
    When I follow "Ajouter"

    Then I fill in "event[title]" with "Forum AFUP 2027"
    And I fill in "event[path]" with "afup-2027"
    And I fill in "event[logoUrl]" with "https://www.fillmurray.com/640/360"
    And I fill in "event[seats]" with "999"
    And I fill in "event[placeName]" with "Paris"
    And I fill in "event[placeAddress]" with "73, rue du chemin"
    And I fill in "event[dateStart]" with "2027-03-03"
    And I fill in "event[dateEnd]" with "2027-03-05"
    And I fill in "event[dateEndCallForProjects]" with "2027-02-05 14:00:00"
    And I fill in "event[dateStartCallForPapers]" with "2027-01-01 08:00:00"
    And I fill in "event[dateEndCallForPapers]" with "2027-02-06 14:00:00"
    And I fill in "event[dateEndVote]" with "2027-03-01 14:00:00"
    And I fill in "event[dateEndPreSales]" with "2027-01-01 14:00:00"
    And I fill in "event[dateEndSpeakersDinerInfosCollection]" with "2027-03-02 14:00:00"
    And I fill in "event[dateEndHotelInfosCollection]" with "2027-03-01 16:00:00"
    And I fill in "event[datePlanningAnnouncement]" with "2027-03-01 16:00:00"
    And I fill in "event[waitingListUrl]" with "https://afup.org/home"
    And I fill in "event[CFP][fr]" with "Appel de candidatures - conférenciers(ères)"
    And I fill in "event[CFP][en]" with "Call for applications - speakers"
    And I fill in "event[CFP][speaker_management_fr]" with "Conférenciers(ères), venez, vous serez très bien pris en charge !"
    And I fill in "event[CFP][speaker_management_en]" with "Speakers, come, you will be very well taken care of!"
    And I fill in "event[CFP][sponsor_management_fr]" with "**Sponsors**, venez, vous serez très visible !"
    And I fill in "event[CFP][sponsor_management_en]" with "**Sponsors**, come, you will be very visible!"
    And I fill in "event[CFP][become_sponsor_description]" with "Le super email de sponsoring"
    And I fill in "event[CFP][mail_inscription_content]" with "Le super email d'inscription"
    And I check "event[speakersDinerEnabled]"
    And I check "event[accomodationEnabled]"
    And I fill in "event[coupons]" with "FREE_FORUM2027,SUPER_FORUM2027"
    And I press "Soumettre"
    Then I should see "Évènement ajouté"
    And I should see "Liste des évènements"
    And I should see "Forum AFUP 2027"
    And I should see "999"
    And I should see "03/03/2027"
    And I should see "05/03/2027"

  Scenario: Suppression d'un évènement vide
    Given I am logged in as admin and on the Administration
    And I follow "Gestion évènements"
    Then the ".content h2" element should contain "Liste des évènements"
    When I follow "Ajouter"
    Then I fill in "event[title]" with "SUPP"
    Then I fill in "event[path]" with "SUPP"
    And I fill in "event[seats]" with "3"
    And I fill in "event[placeName]" with "Paris"
    And I fill in "event[dateStart]" with "1970-01-01"
    And I fill in "event[dateEnd]" with "1970-01-01"
    And I press "Soumettre"
    Then I should see "Évènement ajouté"
    And I follow "Gestion évènements"
    And I should see "Liste des évènements"
    When I follow the button of tooltip "Supprimer le forum SUPP"
    And I should see "Événement supprimé"

  @reloadDbWithTestData
  @clearEmails
  Scenario: Si on tente d'en envoyer un mail de test sans contenu, on a un message d'erreur
    Given I am logged in as admin and on the Administration
    When I go to "/admin/event/edit/1"
    And I fill in "event[CFP][mail_inscription_content]" with ""
    And I press "Soumettre"
    When I go to "/admin/event/edit/1"
    Then I should see "Modifier un évènement"
    When I follow "Envoyer un test du mail d'inscription sur bureau@afup.org"
    Then I should see "Contenu du mail d'inscription non trouvé pour le forum forum"

  Scenario: On arrive bien à ajouter un contenu de mail d'inscription
    Given I am logged in as admin and on the Administration
    When I go to "/admin/event/edit/1"
    And I fill in "event[CFP][mail_inscription_content]" with "Infos à propos de l'évènement"
    And I press "Soumettre"
    Then I should see "Évènement modifié"

  @reloadDbWithTestData
  @clearEmails
  Scenario: Si on tente d'en envoyer un mail de test avec contenu, le mail est bien envoyé
    Given I am logged in as admin and on the Administration
    When I go to "/admin/event/edit/1"
    When I follow "Envoyer un test du mail d'inscription sur bureau@afup.org"
    And I should only receive the following emails:
      | to                | subject         |
      | <bureau@afup.org> | [forum] Merci ! |

  Scenario: On arrive bien à ajouter un fichier au mail d'inscription
    Given I am logged in as admin and on the Administration
    When I go to "/admin/event/edit/1"
    And I should not see "Un fichier joint au mail d'inscription est déjà présent"
    And I attach the file "test_file1.pdf" to "event[registration_email_file]"
    And I press "Soumettre"
    Then I should see "Évènement modifié"

  Scenario: On arrive bien à ajouter les dossiers de sponsoring
    Given I am logged in as admin and on the Administration
    When I go to "/admin/event/edit/1"
    And I should not see "Voir le dossier de sponsoring (FR)"
    And I attach the file "test_file1.pdf" to "event[sponsor_file_fr]"
    And I should not see "Voir le dossier de sponsoring (EN)"
    And I attach the file "test_file1.pdf" to "event[sponsor_file_en]"
    And I press "Soumettre"
    Then I should see "Évènement modifié"
    When I go to "/admin/event/edit/1"
    Then I should see "Voir le dossier de sponsoring (FR)"
    And I should see "Voir le dossier de sponsoring (EN)"

  @clearEmails
  Scenario: Si on tente d'en envoyer un mail de test avec contenu et fichier joint, le mail est bien envoyé avec la pièce jointe
    Given I am logged in as admin and on the Administration
    When I go to "/admin/event/edit/1"
    When I follow "Envoyer un test du mail d'inscription sur bureau@afup.org"
    And I should only receive the following emails:
      | to                | subject         |
      | <bureau@afup.org> | [forum] Merci ! |
    Then the checksum of the attachment "forum.pdf" of the message of id "1" should be "27df44e78e2f3c9a7f331275a4c5b304"
