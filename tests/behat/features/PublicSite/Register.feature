Feature: Site Public - Register

  Scenario: Je ne peux pas adhérer comme particulier avec un email existant
    Given I am on the homepage
    When I follow "Adhérer"
    Then I should see "Devenir membre de l'AFUP"
    When I follow "Adhérer en tant que particulier"
    Then I should see "Formulaire d'inscription à l'AFUP"
    And I fill in "register_user_userCommonInfo_email" with "admin@admin.fr"
    And I press "Créer mon compte"
    And I should see "Un autre compte existe avec cette information: admin@admin.fr"

  Scenario: Je ne peux pas adhérer comme particulier avec un login existant
    Given I am on the homepage
    When I follow "Adhérer"
    Then I should see "Devenir membre de l'AFUP"
    When I follow "Adhérer en tant que particulier"
    Then I should see "Formulaire d'inscription à l'AFUP"
    And I fill in "register_user_userCommonInfo_username" with "admin"
    And I press "Créer mon compte"
    And I should see "Un autre compte existe avec cette information: admin"

  @reloadDbWithTestData
  @clearEmails
  Scenario: Accès à l'adhésion particulier
    Given I am on the homepage
    When I follow "Adhérer"
    Then I should see "Devenir membre de l'AFUP"
    When I follow "Adhérer en tant que particulier"
    Then I should see "Formulaire d'inscription à l'AFUP"
    Then The "register_user_civility" field should only contain the follow values '["M.", "Mme"]'
    When I fill in "register_user_userCommonInfo_lastName" with "Mon nom"
    And I fill in "register_user_userCommonInfo_firstName" with "Mon prénom"
    And I fill in "register_user_userCommonInfo_username" with "lelogin"
    And I fill in "register_user_userCommonInfo_email" with "registeredUser@gmail.com"
    And I fill in "register_user_userCommonInfo_address" with "45 rue des Roses"
    And I fill in "register_user_userCommonInfo_zipcode" with "69003"
    And I fill in "register_user_userCommonInfo_city" with "LYON"
    And I fill in "register_user_country" with "FR"
    And I fill in "register_user_userCommonInfo_password_first" with "test"
    And I fill in "register_user_userCommonInfo_password_second" with "test"
    And I press "Créer mon compte"
    Then I should see "Espace membre"
    And I should see "Merci pour votre inscription. Il ne reste plus qu'à régler votre cotisation."
    When I follow "Se mettre à jour"
    When I press "Régler par carte"
    # Pour suivre la redirection POST de Paybox
    And I submit the form with name "PAYBOX"
    When I fill in "NUMERO_CARTE" with "1111222233334444"
    And I select "12" from "MOIS_VALIDITE"
    And I select "26" from "AN_VALIDITE"
    And I fill in "CVVX" with "123"
    And I press "Valider"
    Then I should see "PAIEMENT ACCEPTÉ"
    When I follow "Retour"
    Then I should see "Le paiement de votre cotisation s'est bien passé, merci."
    # Simuler l'appel de callback Paybox
    And simulate the Paybox callback
    And I should only receive the following emails:
      | to                         | subject                  |
      | <registeredUser@gmail.com> | Votre compte afup.org    |
      | <tresorier@afup.org>       | Paiement cotisation AFUP |

  @reloadDbWithTestData
  @clearEmails
  Scenario: Accès à l'adhésion entreprise
    Given I am on the homepage
    When I follow "Adhérer"
    Then I should see "Devenir membre de l'AFUP"
    When I follow "Adhérer en tant qu'entreprise"
    Then I should see "Adhésion entreprise à l'AFUP"
    When I fill in "company_member_companyName" with "Une société"
    And I fill in "company_member_siret" with "123456789"
    And I fill in "company_member_address" with "45 rue des Roses"
    And I fill in "company_member_zipcode" with "69003"
    And I fill in "company_member_city" with "LYON"
    And I fill in "company_member_firstName" with "Mon prénom de dirigeant"
    And I fill in "company_member_lastName" with "Mon nom de dirigeant"
    And I fill in "company_member_email" with "registeredCompany@gmail.com"
    And I fill in "company_member_phone" with "0123456"
    And I fill in "company_member[invitations][0][email]" with "registeredUser@gmail.com"
    And I press "Enregistrer mon adhésion"
    And I should see "Adhésion enregistrée !"
    And I should see "Montant de la cotisation: 180.00 Euros"
    When I press "Régler par carte"
    # Pour suivre la redirection POST de Paybox
    And I submit the form with name "PAYBOX"
    When I fill in "NUMERO_CARTE" with "1111222233334444"
    And I select "12" from "MOIS_VALIDITE"
    And I select "26" from "AN_VALIDITE"
    And I fill in "CVVX" with "123"
    And I press "Valider"
    Then I should see "PAIEMENT ACCEPTÉ"
    When I follow "Retour"
    Then I should see "Le paiement de votre cotisation s'est bien passé, merci."
    # Simuler l'appel de callback Paybox
    And simulate the Paybox callback
    And I should only receive the following emails:
      | to                         | subject                                                        |
      | <registeredUser@gmail.com> | Une société vous invite à profiter de son compte "Membre AFUP" |
      | <tresorier@afup.org>       | Paiement cotisation AFUP                                       |
