Feature: Site Public - Register

  @reloadDbWithTestData
  Scenario: Accès à l'adhésion particulier
    Given I am on the homepage
    When I follow "Adhérer"
    Then I should see "Devenir membre de l'AFUP"
    When I follow "Adhérer en tant que particulier"
    Then I should see "Formulaire d'incription à l'AFUP"
    Then The "civilite" field should only contain the follow values '["M.", "Mme", "Mlle"]'
