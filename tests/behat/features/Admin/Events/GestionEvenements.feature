Feature: Administration - Évènements - Gestions Évènements

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
    Then I should see "Gestion d'évènement"
    And I press "Soumettre"
    Then I should see "Titre du forum manquant"
    And I should see "Nombre de places manquant"

  Scenario: On crée un nouvel évènement
    Given I am logged in as admin and on the Administration
    And I follow "Gestion évènements"
    Then the ".content h2" element should contain "Liste des évènements"
    When I follow "Ajouter"

    Then I fill in "titre" with "Forum AFUP 2027"
    And I fill in "path" with "afup-2027"
    And I fill in "trello_list_id" with "4242"
    And I fill in "logo_url" with "https://www.fillmurray.com/640/360"
    And I fill in "nb_places" with "999"
    And I fill in "place_name" with "Paris"
    And I fill in "place_address" with "73, rue du chemin"
    # date_debut
    And I select "3" from "date_debut[d]"
    And I select "3" from "date_debut[M]"
    And I select "2027" from "date_debut[Y]"
    # date_fin
    And I select "6" from "date_fin[d]"
    And I select "3" from "date_fin[M]"
    And I select "2027" from "date_fin[Y]"
    # date_fin_appel_projet
    And I select "1" from "date_fin_appel_projet[d]"
    And I select "1" from "date_fin_appel_projet[M]"
    And I select "2027" from "date_fin_appel_projet[Y]"
    And I select "14" from "date_fin_appel_projet[H]"
    And I select "0" from "date_fin_appel_projet[i]"
    And I select "0" from "date_fin_appel_projet[s]"
    # date_fin_appel_conferencier
    And I select "1" from "date_fin_appel_conferencier[d]"
    And I select "2" from "date_fin_appel_conferencier[M]"
    And I select "2027" from "date_fin_appel_conferencier[Y]"
    And I select "14" from "date_fin_appel_conferencier[H]"
    And I select "0" from "date_fin_appel_conferencier[i]"
    And I select "0" from "date_fin_appel_conferencier[s]"
    And I check "vote_enabled"
    # date_fin_vote
    And I select "1" from "date_fin_vote[d]"
    And I select "3" from "date_fin_vote[M]"
    And I select "2027" from "date_fin_vote[Y]"
    And I select "14" from "date_fin_vote[H]"
    And I select "0" from "date_fin_vote[i]"
    And I select "0" from "date_fin_vote[s]"
    # date_fin_prevente
    And I select "1" from "date_fin_prevente[d]"
    And I select "1" from "date_fin_prevente[M]"
    And I select "2027" from "date_fin_prevente[Y]"
    And I select "14" from "date_fin_prevente[H]"
    And I select "0" from "date_fin_prevente[i]"
    And I select "0" from "date_fin_prevente[s]"
    # date_fin_vente
    And I select "2" from "date_fin_vente[d]"
    And I select "3" from "date_fin_vente[M]"
    And I select "2027" from "date_fin_vente[Y]"
    And I select "14" from "date_fin_vente[H]"
    And I select "0" from "date_fin_vente[i]"
    And I select "0" from "date_fin_vente[s]"
    # date_fin_saisie_repas_speakers
    And I select "2" from "date_fin_saisie_repas_speakers[d]"
    And I select "3" from "date_fin_saisie_repas_speakers[M]"
    And I select "2027" from "date_fin_saisie_repas_speakers[Y]"
    And I select "14" from "date_fin_saisie_repas_speakers[H]"
    And I select "0" from "date_fin_saisie_repas_speakers[i]"
    And I select "0" from "date_fin_saisie_repas_speakers[s]"
    # date_fin_saisie_nuites_hotel
    And I select "1" from "date_fin_saisie_nuites_hotel[d]"
    And I select "3" from "date_fin_saisie_nuites_hotel[M]"
    And I select "2027" from "date_fin_saisie_nuites_hotel[Y]"
    And I select "16" from "date_fin_saisie_nuites_hotel[H]"
    And I select "0" from "date_fin_saisie_nuites_hotel[i]"
    And I select "0" from "date_fin_saisie_nuites_hotel[s]"
    # date_annonce_planning
    And I select "1" from "date_annonce_planning[d]"
    And I select "3" from "date_annonce_planning[M]"
    And I select "2027" from "date_annonce_planning[Y]"
    And I select "16" from "date_annonce_planning[H]"
    And I select "0" from "date_annonce_planning[i]"
    And I select "0" from "date_annonce_planning[s]"
    And I fill in "waiting_list_url" with "https://afup.org/home"
    And I fill in "cfp_fr" with "Appel de candidatures - conférenciers(ères)"
    And I fill in "cfp_en" with "Call for applications - speakers"
    And I fill in "speaker_management_fr" with "Conférenciers(ères), venez, vous serez très bien pris en charge !"
    And I fill in "speaker_management_en" with "Speakers, come, you will be very well taken care of!"
    And I fill in "sponsor_management_fr" with "**Sponsors**, venez, vous serez très visible !"
    And I fill in "sponsor_management_en" with "**Sponsors**, come, you will be very visible!"
    And I fill in "mail_inscription_content" with "Le super email d'inscription"
    And I fill in "become_sponsor_description" with "Le super email de sponsoring"
    And I check "speakers_diner_enabled"
    And I check "accomodation_enabled"
    And I fill in "coupons" with "FREE_FORUM2027,SUPER_FORUM2027"

    And I press "Soumettre"
    Then I should see "Le forum a été ajouté"
    And I should see "Liste des évènements"
    And I should see "Forum AFUP 2027"
    And I should see "999"
    And I should see "03/03/2027"
    And I should see "06/03/2027"

 Scenario: Si on tente d'en envoyer un mail de test sans contenu, on a un message d'erreur
    Given I am logged in as admin and on the Administration
    When I go to "/pages/administration/index.php?page=forum_gestion&action=modifier&id=1"
    Then I should see "Modifier un évènement"
    When I follow "Envoyer un test du mail d'inscription sur bureau@afup.org"
    Then I should see "Contenu du mail d'inscription non trouvé pour le forum forum"

  Scenario: On arrive bien à ajouter un contenu de mail d'inscription
    Given I am logged in as admin and on the Administration
    When I go to "/pages/administration/index.php?page=forum_gestion&action=modifier&id=1"
    And I fill in "mail_inscription_content" with "Infos à propos de l'évènement"
    And I press "Soumettre"
    Then I should see "Le forum a été modifié"

  Scenario: Si on tente d'en envoyer un mail de test avec contenu, le mail est bien envoyé
    Given I am logged in as admin and on the Administration
    When I go to "/pages/administration/index.php?page=forum_gestion&action=modifier&id=1"
    When I follow "Envoyer un test du mail d'inscription sur bureau@afup.org"
    And I should only receive the following emails:
      | to                                     | subject         |
      | <bureau@afup.org>,<tresorier@afup.org> | [forum] Merci ! |

  Scenario: On arrive bien à ajouter un fichier au mail d'inscription
    Given I am logged in as admin and on the Administration
    When I go to "/pages/administration/index.php?page=forum_gestion&action=modifier&id=1"
    And I should not see "Un fichier joint au mail d'inscription est déjà présent"
    And I attach the file "test_file1.pdf" to "mail_inscription_attachment"
    And I press "Soumettre"
    Then I should see "Le forum a été modifié"

  Scenario: On arrive bien à ajouter les dossiers de sponsoring
    Given I am logged in as admin and on the Administration
    When I go to "/pages/administration/index.php?page=forum_gestion&action=modifier&id=1"
    And I should not see "Voir le dossier de sponsoring (FR)"
    And I attach the file "test_file1.pdf" to "file_sponsor_fr"
    And I should not see "Voir le dossier de sponsoring (EN)"
    And I attach the file "test_file1.pdf" to "file_sponsor_en"
    And I press "Soumettre"
    Then I should see "Le forum a été modifié"
    When I go to "/pages/administration/index.php?page=forum_gestion&action=modifier&id=1"
    Then I should see "Voir le dossier de sponsoring (FR)"
    And I should see "Voir le dossier de sponsoring (EN)"

  @clearEmails
  Scenario: Si on tente d'en envoyer un mail de test avec contenu et fichier joint, le mail est bien envoyé avec la pièce jointe
    Given I am logged in as admin and on the Administration
    When I go to "/pages/administration/index.php?page=forum_gestion&action=modifier&id=1"
    When I follow "Envoyer un test du mail d'inscription sur bureau@afup.org"
    And I should only receive the following emails:
      | to                                     | subject         |
      | <bureau@afup.org>,<tresorier@afup.org> | [forum] Merci ! |
    Then the checksum of the attachment "forum.pdf" of the message of id "1" should be "27df44e78e2f3c9a7f331275a4c5b304"
