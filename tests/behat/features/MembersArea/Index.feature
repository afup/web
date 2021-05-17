Feature: Espace membre, accueil

  @reloadDbWithTestData
  Scenario: L'accueil affiche le nom et prénom du compte
    Given I am logged in as admin
    And I follow "Espace membre"
    Then I should see "Espace membre"
    And I should see "Mes coordonnées : Admin Admin"

  @reloadDbWithTestData
  Scenario: On arrive bien à définir son antenne la plus proche
    Given I am logged in as admin
    And I follow "Espace membre"
    Then I should see "Espace membre"
    And I should see "Antenne la plus proche : Aucune"
    When I follow "Définir"
    Then I should see "Mes coordonnées"

  @reloadDbWithTestData
  Scenario: On arrive bien à définir changer ses coordonnées
    Given I am logged in as admin
    And I follow "Espace membre"
    Then I should see "Espace membre"
    And I should see "Antenne la plus proche : Aucune"
    When I follow "Définir"
    Then I should see "Mes coordonnées"
    When I fill in "contact_details[nearest_office]" with "lyon"
    And I fill in "contact_details[address]" with "42 rue des lilas"
    And I fill in "contact_details[zipcode]" with "75001"
    And I fill in "contact_details[city]" with "Lyon Cedex"
    And I press "Modifier"
    Then I should see "Votre compte a été modifié"
    When I follow "Espace membre"
    Then I should see "Antenne la plus proche : Lyon"
    When I follow "Modifier"
    Then the "contact_details[address]" field should contain "42 rue des lilas"
    And the "contact_details[zipcode]" field should contain "75001"
    And the "contact_details[city]" field should contain "Lyon Cedex"
