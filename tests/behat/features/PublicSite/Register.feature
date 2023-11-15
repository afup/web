Feature: Site Public - Register

  @reloadDbWithTestData
  @clearEmails
  Scenario: Accès à l'adhésion particulier
    Given I am on the homepage
    When I follow "Adhérer"
    Then I should see "Devenir membre de l'AFUP"
    When I follow "Adhérer en tant que particulier"
    Then I should see "Formulaire d'incription à l'AFUP"
    Then The "civilite" field should only contain the follow values '["M.", "Mme"]'
    When I fill in "nom" with "Mon nom"
    And I fill in "prenom" with "Mon prénom"
    And I fill in "login" with "lelogin"
    And I fill in "email" with "registeredUser@gmail.com"
    And I fill in "adresse" with "45 rue des Roses"
    And I fill in "code_postal" with "69003"
    And I fill in "ville" with "LYON"
    And I fill in "mot_de_passe" with "test"
    And I fill in "confirmation_mot_de_passe" with "test"
    And I press "Ajouter"
    Then I should see "Espace membre"
    And I should see "Merci pour votre inscription. Il ne reste plus qu'à régler votre cotisation."
    When I follow "Se mettre à jour"
    When I press "Régler par carte"
    # Pour suivre la redirection POST de Paybox
    And I submit the form with name "PAYBOX"
    When I fill in "NUMERO_CARTE" with "1111222233334444"
    And I select "12" from "MOIS_VALIDITE"
    And I select "25" from "AN_VALIDITE"
    And I fill in "CVVX" with "123"
    And I press "Valider"
    Then I should see "PAIEMENT ACCEPTÉ"
    When I follow "Retour"
    Then I should see "Le paiement de votre cotisation s'est bien passé, merci."
    # Simuler l'appel de callback Paybox
    And simulate the Paybox callback
    And I should only receive the following emails:
      | to                         | subject               |
      | <registeredUser@gmail.com> | Votre compte afup.org |

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
    And I should see "Montant de la cotisation: 150.00 Euros"
    When I press "Régler par carte"
    # Pour suivre la redirection POST de Paybox
    And I submit the form with name "PAYBOX"
    When I fill in "NUMERO_CARTE" with "1111222233334444"
    And I select "12" from "MOIS_VALIDITE"
    And I select "25" from "AN_VALIDITE"
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
