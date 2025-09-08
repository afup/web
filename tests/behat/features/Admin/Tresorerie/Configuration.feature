Feature: Administration - Trésorerie - Configuration

  @reloadDbWithTestData
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

  @reloadDbWithTestData
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

  @reloadDbWithTestData
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

  @reloadDbWithTestData
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

  @reloadDbWithTestData
  Scenario: Création/liste des comptes
    Given I am logged in as admin and on the Administration
    When I follow "Configuration"
    Then the ".content h2" element should contain "Configuration"
    When I follow "Comptes"
    When I follow "Ajouter"
    Then the ".content h2" element should contain "Ajouter un compte"
    When I fill in "account[name]" with "Un super compte"
    And I press "Ajouter"
    Then the ".content .message" element should contain "Le compte Un super compte a été créé"
    And I should see "Un super compte"

  Scenario: Archivage d'un compte
    Given I am logged in as admin and on the Administration
    When I follow "Configuration"
    Then the ".content h2" element should contain "Configuration"
    When I follow "Comptes"
    And I follow the button of tooltip "Modifier la ligne Un super compte"
    Then the "account[name]" field should contain "Un super compte"
    When I follow "Archiver ce compte"
    Then I should see "Ce compte est archivé"

  @reloadDbWithTestData
  Scenario: Création/liste des règles
    Given I am logged in as admin and on the Administration
    When I follow "Configuration"
    Then the ".content h2" element should contain "Configuration"
    When I follow "Règles"
    When I follow "Ajouter"
    Then the ".content h2" element should contain "Ajouter une règle"
    When I fill in "label" with "Une nouvelle règle"
    When I fill in "condition" with "REM INSCRIPTION"
    When I fill in "is_credit" with "1"
    # CB
    When I fill in "mode_regl_id" with "2"
    # TVA à 5.5%
    When I fill in "vat" with "5_5"
    # À determiner
    When I fill in "category_id" with "26"
    # Association AFUP
    When I fill in "event_id" with "27"
    # Justification obligatoire
    When I fill in "attachment_required" with "1"
    And I press "soumettre"
    Then the ".content .message" element should contain "La règle a été ajoutée"
    And I should see "Une nouvelle règle REM INSCRIPTION"

    When I follow the button of tooltip "Modifier la règle Une nouvelle règle"
    And the "label" field should contain "Une nouvelle règle"
    And the "condition" field should contain "REM INSCRIPTION"
    And The "is_credit" field should have the following selected value "1"
    And The "mode_regl_id" field should have the following selected value "2"
    And The "vat" field should have the following selected value "5_5"
    And The "category_id" field should have the following selected value "26"
    And The "event_id" field should have the following selected value "27"
    And The "attachment_required" field should have the following selected value "1"
