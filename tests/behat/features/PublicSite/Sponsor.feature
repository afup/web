Feature: Site Public - Devenir sponsor

  @reloadDbWithTestData
  @clearEmails
  Scenario: Devenir sponsor - tous les champs remplis
    Given I go to "/event/forum/sponsor/become-sponsor"
    Then I should see "Recevoir notre dossier"
    And I should not see "Sponsorisez l’AFUP !"
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
      | <email@domain.com>   | Dossier de sponsoring AFUP                        |

  @reloadDbWithTestData
  @clearEmails
  Scenario: Devenir sponsor - tous les champs requis remplis
    Given I go to "/event/forum/sponsor/become-sponsor"
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
      | <email@domain.com>   | Dossier de sponsoring AFUP                        |

  @clearAllSponsorFiles
  Scenario: Page générique indisponible sans dossier de sponsoring
    Given I go to "/become-sponsor"
    Then the response status code should be 404

  @reloadDbWithTestData
  @clearEmails
  @withSponsorshipFile
  Scenario: Devenir sponsor depuis la page générique
    Given I go to "/become-sponsor"
    Then the "h1" element should contain "Devenir sponsor"
    And I should see "Sponsorisez l’AFUP !"
    And I should see "Recevoir notre dossier"
    And The "lead_language" field should only contain the follow values '["fr", "en"]'
    When I fill in "lead_firstname" with "Prénom"
    And I fill in "lead_lastname" with "Nom"
    And I fill in "lead_email" with "email@domain.com"
    And I fill in "lead_phone" with "0600000000"
    And I fill in "lead_company" with "company"
    And I press "Recevoir le dossier"
    Then the url should match "/become-sponsor/thank-you"
    And I should see "Merci"
    And I should see "Vous allez recevoir le dossier par mail dans quelques minutes."
    And I should only receive the following emails:
      | to                   | subject                                   |
      | <sponsors@afup.org>  | Nouvelle demande de dossier de sponsoring |
      | <email@domain.com>   | Dossier de sponsoring AFUP                |

  @reloadDbWithTestData
  @clearEmails
  @withSponsorshipFile
  Scenario: Sans dossier dans la langue demandée, on envoie celui d'une autre langue
    Given I go to "/become-sponsor"
    When I fill in "lead_firstname" with "Prénom"
    And I fill in "lead_lastname" with "Nom"
    And I fill in "lead_email" with "email@domain.com"
    And I fill in "lead_phone" with "0600000000"
    And I fill in "lead_company" with "company"
    And I select "en" from "lead_language"
    And I press "Recevoir le dossier"
    Then the url should match "/become-sponsor/thank-you"
    And the checksum of the attachment "pack-sponsoring-afup-fr.pdf" of the message of id "2" should be "27df44e78e2f3c9a7f331275a4c5b304"
