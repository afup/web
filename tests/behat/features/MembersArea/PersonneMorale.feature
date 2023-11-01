Feature: Espace membre > Personne morale > Personnes rattachés

  @reloadDbWithTestData
  @clearEmails
  Scenario: Gestions des droits
    Given I am logged-in with the user "edmonddupont" and the password "edmonddupont"
    And I follow "Espace membre"
    Then I should see "Espace membre"
    And I follow "Modifier les personnes rattachées"
    # Liste des membres
    Then I should see "Les membres rattachés à mon entreprise"
    Then I should see "Jan Raoul Non jean.raoul@mycorp.fr"
    # Donner les droits
    And I press "Donner les droits de gestion"
    Then I should see "Le membre a été promu en tant que manager."
    # Retirer les droits
    And I press "Enlever les droits de gestion"
    Then I should see "Le membre n'a plus accès la gestion de l'entreprise."

  @reloadDbWithTestData
  @clearEmails
  Scenario: Invitation des membres
    Given I am logged-in with the user "edmonddupont" and the password "edmonddupont"
    And I follow "Espace membre"
    Then I should see "Espace membre"
    And I follow "Modifier les personnes rattachées"
    # Envoyer une invitation
    Then I fill in "company_member_invitation[email]" with "email1@email.com"
    And I press "Envoyer l'invitation"
    Then I should see "L'invitation a été envoyée à l'adresse email1@email.com."
    And I should only receive the following emails:
      | to                 | subject                                                   |
      | <email1@email.com> | MyCorp vous invite à profiter de son compte "Membre AFUP" |
    And I should see "email1@email.com Non"
    # Envoyer la dernière
    Then I fill in "company_member_invitation[email]" with "email2@email.com"
    And I press "Envoyer l'invitation"
    # Renvoyer la dernière invitation
    And I press "Envoyer à nouveau"
    And I should see "L'invitation a été renvoyée."
    And I should only receive the following emails:
      | to                 | subject                                                   |
      | <email1@email.com> | MyCorp vous invite à profiter de son compte "Membre AFUP" |
      | <email2@email.com> | MyCorp vous invite à profiter de son compte "Membre AFUP" |
      | <email1@email.com> | MyCorp vous invite à profiter de son compte "Membre AFUP" |
    # Invitation max bloqué
    Then I should see "Vous avez actuellement 1 membre(s) rattaché(s) et 2 invitation(s) en attente."
    # Annulation d'une invitation
    And I press "Annuler"
    Then I should see "L'invitation a été annulée."
    Then I should not see "Vous avez actuellement 1 membre(s) rattaché(s) et 2 invitation(s) en attente."
