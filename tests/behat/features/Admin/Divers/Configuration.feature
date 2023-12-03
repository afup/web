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
    # on remet la valeur d'origine vu qu'on modifie un fichier
    # ce n'est pas idéal, mais à terme il faudrait plutôt qu'on ne modifie pas le fichier
    # et que les infos pertinentes à modifier le soient en base et le reste soit dans de la conf statique
    And I fill in "afup|adresse" with "32, Boulevard de Strasbourg CS 30108"
    When I press "Enregistrer"
    And I should see "32, Boulevard de Strasbourg CS 30108"
