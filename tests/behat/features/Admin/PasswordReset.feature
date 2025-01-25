Feature: Password Reset

@reloadDbWithTestData
@clearEmails
Scenario: L'utilisateur reçoit une URL de connexion complète dans l'e-mail de réinitialisation du mot de passe.
    When I request a password reset for "edmond.dupont@mycorp.fr"
    Then I should receive an email
    And the email should contain a full URL starting with "https://apachephptest:80/admin/login"
