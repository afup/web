Feature: Administration - Partie Evenements

  @reloadDbWithTestData
  Scenario: Accès à la liste à l'ajout d'une inscription
    Given I am logged in as admin and on the Administration
    And I follow "Inscriptions"
    Then the ".content h2" element should contain "Inscriptions"
    # ajout d'une rubrique
    When I follow "Ajouter"
    Then I should see "Ajouter une inscription pour le forum"
    Then The "civilite" field should only contain the follow values '["M.", "Mme"]'
    When I select "2" from "type_inscription"
    And I fill in "nom" with "Nom participant"
    And I fill in "prenom" with "Prénom participant"
    And I fill in "email" with "nomparticipant@gmail.com"
    And I select "2" from "type_reglement"
    And I press "Soumettre"
    Then I should see "L'inscription a été ajoutée"
    And I should see "Inscriptions"
    And I should see "Prénom participant Nom participant"


  @reloadDbWithTestData
  Scenario: Tickets sponsors
    Given I am logged in as admin and on the Administration
    And I follow "Tokens sponsors"
    Then the ".content h2" element should contain "Liste des tokens sponsors pour forum"
    # ajout d'un token
    When I fill in "sponsor_token[company]" with "Ma société"
    And I fill in "sponsor_token[contactEmail]" with "testToken1@mail.com"
    And I fill in "sponsor_token[maxInvitations]" with "1"
    And I press "Enregistrer"
    # Listing des tokens
    Then I should see "Invitations totales"
    When I press "Voir les invitations"
    # Listing des invitations vides
    Then I should see "Billetterie sponsor: forum - Ma société"
    And I should see "VOS PLACES GRATUITES"
    And I should see "Aucun ticket enregistré pour le moment."
    And I should see "Nouveau ticket"
    # Ajout d'une invitation
    And The "sponsor_ticket[civility]" field should only contain the follow values '["M.", "Mlle", "Mme"]'
    When I fill in "sponsor_ticket[firstname]" with "Nom personne invitée"
    And I fill in "sponsor_ticket[lastname]" with "Prénom personne invitée"
    And I fill in "sponsor_ticket[email]" with "personneinvitee@masociete.com"
    And I press "Enregistrer"
    # Listing des invitations renseignées
    Then I should see "personneinvitee@masociete.com"
