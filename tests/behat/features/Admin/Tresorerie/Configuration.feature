Feature: Administration - Trésorerie - Configuration

  Scenario: Création/liste des évènements
    Given I am logged in as admin and on the Administration
    When I follow "Configuration"
    Then the ".content h2" element should contain "Configuration"
    When I follow "Évènements"
    When I follow "Ajouter"
    Then the ".content h2" element should contain "Ajouter une ligne configuration 'Evenement'"
    When I fill in "evenement" with "Un super évènement"
    And I press "soumettre"
    Then the ".content .message" element should contain "L'écriture a été ajoutée"
    And I should see "Un super évènement"

  Scenario: Création/liste des catégories
    Given I am logged in as admin and on the Administration
    When I follow "Configuration"
    Then the ".content h2" element should contain "Configuration"
    When I follow "Catégories"
    When I follow "Ajouter"
    Then the ".content h2" element should contain "Ajouter une categorie"
    When I fill in "categorie" with "Une super catégorie"
    And I press "soumettre"
    Then the ".content .message" element should contain "L'écriture a été ajoutée"
    And I should see "Une super catégorie"

  Scenario: Création/liste des opérations
    Given I am logged in as admin and on the Administration
    When I follow "Configuration"
    Then the ".content h2" element should contain "Configuration"
    When I follow "Opérations"
    When I follow "Ajouter"
    Then the ".content h2" element should contain "Ajouter une opération"
    When I fill in "operation" with "Une super opération"
    And I press "soumettre"
    Then the ".content .message" element should contain "L'écriture a été ajoutée"
    And I should see "Une super opération"

  Scenario: Création/liste des opérations
    Given I am logged in as admin and on the Administration
    When I follow "Configuration"
    Then the ".content h2" element should contain "Configuration"
    When I follow "Modes de réglements"
    When I follow "Ajouter"
    Then the ".content h2" element should contain "Ajouter un type de reglement"
    When I fill in "reglement" with "Un super règlement"
    And I press "soumettre"
    Then the ".content .message" element should contain "L'écriture a été ajoutée"
    And I should see "Un super règlement"

  Scenario: Création/liste des comptes
    Given I am logged in as admin and on the Administration
    When I follow "Configuration"
    Then the ".content h2" element should contain "Configuration"
    When I follow "Comptes"
    When I follow "Ajouter"
    Then the ".content h2" element should contain "Ajouter un compte"
    When I fill in "nom_compte" with "Un super compte"
    And I press "soumettre"
    Then the ".content .message" element should contain "L'écriture a été ajoutée"
    And I should see "Un super compte"

