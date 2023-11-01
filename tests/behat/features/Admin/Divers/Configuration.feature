Feature: Administration - Configuration

  @reloadDbWithTestData
  Scenario: Afficher / modifier l'adresse
    Given I am logged in as admin and on the Administration
    And I follow "Configuration du site"
    Then I should see "Configuration"
    And I should see "32, Boulevard de Strasbourg CS 30108"
    And I fill in "afup|adresse" with "32, Boulevard de Nantes CS 30108"
    When I press "Enregistrer"
    Then the ".content .message" element should contain "La configuration a été enregistrée"
    And I should see "32, Boulevard de Nantes CS 30108"
