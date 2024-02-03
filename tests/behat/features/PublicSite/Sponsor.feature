Feature: Site Public - Devenir sponsor

  @reloadDbWithTestData
  Scenario: Redirection depuis /become-sponsor vers la page de sponsoring du dernier évènement
    Given I go to "/become-sponsor"
    Then the url should match "/event/forum/sponsor/become-sponsor"
    And the ".container h1" element should contain "Devenir sponsor"

  @reloadDbWithTestData
  @clearEmails
  Scenario: Devenir sponsor - tous les champs remplis
    Given I go to "/become-sponsor"
    Then I should see "Recevoir notre dossier"
    Then The "lead_language" field should only contain the follow values '["fr", "en"]'
    When I fill in "lead_firstname" with "Prénom"
    And I fill in "lead_lastname" with "Nom"
    And I fill in "lead_email" with "email@domain.com"
    And I fill in "lead_phone" with "0600000000"
    And I fill in "lead_company" with "company"
    And I fill in "lead_poste" with "dev"
    And I fill in "lead_website" with "my-website.fr"
    And I press "Recevoir le dossier"
    Then I should not see "Cette valeur ne doit pas être vide."
    And I should not see "Cette valeur n'est pas une adresse email valide."
    And I should see "Merci"
    And I should see "Vous allez recevoir le dossier par mail dans quelques minutes."
    And I should only receive the following emails:
      | to                   | subject                                           |
      | <sponsors@afup.org>  | forum - Nouvelle demande de dossier de sponsoring |
      | <email@domain.com>   |  Dossier de sponsoring forum                      |

  @reloadDbWithTestData
  @clearEmails
  Scenario: Devenir sponsor - tous les champs requis remplis
    Given I go to "/become-sponsor"
    Then I should see "Recevoir notre dossier"
    Then The "lead_language" field should only contain the follow values '["fr", "en"]'
    When I fill in "lead_firstname" with "Prénom"
    And I fill in "lead_lastname" with "Nom"
    And I fill in "lead_email" with "email@domain.com"
    And I fill in "lead_phone" with "0600000000"
    And I fill in "lead_company" with "company"
    And I press "Recevoir le dossier"
    Then I should not see "Cette valeur ne doit pas être vide."
    And I should not see "Cette valeur n'est pas une adresse email valide."
    And I should see "Merci"
    And I should see "Vous allez recevoir le dossier par mail dans quelques minutes."
    And I should only receive the following emails:
      | to                   | subject                                           |
      | <sponsors@afup.org>  | forum - Nouvelle demande de dossier de sponsoring |
      | <email@domain.com>   |  Dossier de sponsoring forum                      |
