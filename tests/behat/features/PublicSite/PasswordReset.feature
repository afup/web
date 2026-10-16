Feature: Password Reset

@reloadDbWithTestData
@clearEmails
Scenario: L'utilisateur reçoit un lien de réinitialisation dans l'e-mail.
    When I request a password reset for "edmond.dupont@mycorp.fr"
    Then I should receive an email
    And the email should contain a full URL starting with "https://apachephptest:80/password-reset/"

# Prévention contre l'énumération des comptes
Scenario: Un message générique est affiché si on soumet un email inconnu
    When I request a password reset for "unkown.email@example.com"
    Then I should see "Votre demande a été prise en compte."

@reloadDbWithTestData
@clearEmails
Scenario: L'utilisateur choisit un nouveau mot de passe et se connecte avec
    When I request a password reset for "edmond.dupont@mycorp.fr"
    And I follow the password reset link received by email
    Then I should see "Nouveau mot de passe"
    When I fill in "reset_password_plainPassword_first" with "correcte cheval batterie agrafe"
    And I fill in "reset_password_plainPassword_second" with "correcte cheval batterie agrafe"
    And I press "Enregistrer mon nouveau mot de passe"
    Then I should see "Votre mot de passe a été modifié."
    And I am logged-in with the user "edmonddupont" and the password "correcte cheval batterie agrafe"

@reloadDbWithTestData
@clearEmails
Scenario: Un lien expiré ne permet plus de changer le mot de passe
    When I request a password reset for "edmond.dupont@mycorp.fr"
    Given the current date is "+31 minutes"
    When I follow the password reset link received by email
    Then I should see "Ce lien n'est plus valide."

@reloadDbWithTestData
@clearEmails
Scenario: Un lien déjà utilisé ne fonctionne plus
    When I request a password reset for "edmond.dupont@mycorp.fr"
    And I follow the password reset link received by email
    And I fill in "reset_password_plainPassword_first" with "correcte cheval batterie agrafe"
    And I fill in "reset_password_plainPassword_second" with "correcte cheval batterie agrafe"
    And I press "Enregistrer mon nouveau mot de passe"
    Then I should see "Votre mot de passe a été modifié."
    When I follow the password reset link received previously
    Then I should see "Ce lien n'est plus valide."

@reloadDbWithTestData
@clearEmails
Scenario: Une seconde demande rend le premier lien inopérant
    When I request a password reset for "edmond.dupont@mycorp.fr"
    And I follow the password reset link received by email
    Then I should see "Nouveau mot de passe"
    When I request a password reset for "edmond.dupont@mycorp.fr"
    And I follow the password reset link received previously
    Then I should see "Ce lien n'est plus valide."
