Feature: Administration - Configuration

  @reloadDbWithTestData
  Scenario: Afficher / modifier l'adresse
    Given I am logged in as admin and on the Administration
    And I follow "Configuration du site"
    Then I should see "Configuration"
    And I should see "Paris Cedex 10"
    And I fill in "afup|ville" with "Paris Cedex 12"
    When I press "Enregistrer"
    Then the ".content .message" element should contain "La configuration a été enregistrée"
    And I should see "Paris Cedex 12"
    # on remet la valeur d'origine vu qu'on modifie un fichier
    # ce n'est pas idéal, mais à terme il faudrait plutôt qu'on ne modifie pas le fichier
    # et que les infos pertinentes à modifier le soient en base et le reste soit dans de la conf statique
    And I fill in "afup|adresse" with "Paris Cedex 10"
    When I press "Enregistrer"
    And I should see "Paris Cedex 10"
