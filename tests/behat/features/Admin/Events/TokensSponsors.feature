Feature: Administration - Évènements - Tokens Sponsors

  @reloadDbWithTestData
  @clearEmails
  Scenario: Tickets sponsors
    Given I am logged in as admin and on the Administration
    And I follow "Tokens sponsors"
    Then the ".content h2" element should contain "Liste des tokens sponsors pour forum"
    # ajout d'un token
    When I fill in "sponsor_token[company]" with "Ma société"
    And I fill in "sponsor_token[contactEmail]" with "testToken1@mail.com"
    And I fill in "sponsor_token[maxInvitations]" with "2"
    And I press "Enregistrer"
    # Listing des tokens
    Then I should see "Invitations totales"
    When I press "Voir les invitations"
    # Listing des invitations vides
    Then I should see "Billetterie sponsor: forum - Ma société"
    And I should see "VOS PLACES GRATUITES"
    And I should see "Aucun ticket enregistré pour le moment."
    And I should see "Nouveau ticket"
    # Vérifie le markdown depuis sponsor_management_
    And the response should contain "<strong>Sponsors</strong>, venez, vous serez très visible !"
    # Ajout d'une invitation
    And The "sponsor_ticket[civility]" field should only contain the follow values '["M.", "Mme"]'
    When I fill in "sponsor_ticket[firstname]" with "Nom personne invitée"
    And I fill in "sponsor_ticket[lastname]" with "Prénom personne invitée"
    And I fill in "sponsor_ticket[email]" with "personneinvitee@masociete.com"
    And I press "Enregistrer"
    # Listing des invitations renseignées
    Then I should see "personneinvitee@masociete.com"
    # La modification d'une place ne modifie pas le nombre de places disponibles
    And I should see "Places disponibles: 1 / 2"
    When I follow "Modifier"
    And I fill in "sponsor_ticket[lastname]" with "Prénom personne invitée modifée"
    And I press "Enregistrer"
    Then I should see "Places disponibles: 1 / 2 "
    # Envoi de l'email de rappel
    When I am on "/admin/event/sponsor"
    Then I follow "Envoyer le mail de rappel"
    And I should see "1 mails de relance ont été envoyés"
    And I should only receive the following emails:
      | to                    | subject                                                      |
      | <testToken1@mail.com> | Toutes les informations concernant votre sponsoring du forum |
      | <testToken1@mail.com> | Sponsor: enregistrez-vous pour le forum                      |
