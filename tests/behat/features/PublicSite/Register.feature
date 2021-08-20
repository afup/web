Feature: Site Public - Register

  @reloadDbWithTestData
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
    And I should see " Merci pour votre inscription. Il ne reste plus qu'à régler votre cotisation."
