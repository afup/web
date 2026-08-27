Feature: Password Reset

@reloadDbWithTestData
@clearEmails
Scenario: L'utilisateur reçoit une URL de connexion complète dans l'e-mail de réinitialisation du mot de passe.
    When I request a password reset for "edmond.dupont@mycorp.fr"
    Then I should receive an email
    And the email should contain a full URL starting with "https://apachephptest:80/login"

# Prévention contre l'énumération des comptes
Scenario: Un message générique est affiché si on soumet un email inconnu
    When I request a password reset for "unkown.email@example.com"
    Then I should see "Votre demande a été prise en compte. Si un compte correspond à cet email vous recevez un nouveau mot de passe rapidement."
