Feature: Administration - Trésorerie - Configuration

  @reloadDbWithTestData
  Scenario: Création/liste des évènements
    Given I am logged in as admin and on the Administration
    When I follow "Configuration"
    Then the ".content h2" element should contain "Configuration"
    When I follow "Évènements"
    When I follow "Ajouter"
    Then the ".content h2" element should contain "Ajouter un évènement"
    When I fill in "event[name]" with "Un super évènement"
    And I press "Ajouter"
    Then the ".content .message" element should contain "L'évènement a été ajouté"
    And I should see "Un super évènement"

  @reloadDbWithTestData
  Scenario: Création/liste des catégories
    Given I am logged in as admin and on the Administration
    When I follow "Configuration"
    Then the ".content h2" element should contain "Configuration"
    When I follow "Catégories"
    When I follow "Ajouter"
    Then the ".content h2" element should contain "Ajouter une catégorie"
    When I fill in "category[name]" with "Une super catégorie"
    And I press "Ajouter"
    Then the ".content .message" element should contain "La catégorie a été ajoutée"
    And I should see "Une super catégorie"

  @reloadDbWithTestData
  Scenario: Création/liste des opérations
    Given I am logged in as admin and on the Administration
    When I follow "Configuration"
    Then the ".content h2" element should contain "Configuration"
    When I follow "Opérations"
    When I follow "Ajouter"
    Then the ".content h2" element should contain "Ajouter une opération"
    When I fill in "operation[name]" with "Une super opération"
    And I press "Ajouter"
    Then the ".content .message" element should contain "L'opération a été ajoutée"
    And I should see "Une super opération"

  @reloadDbWithTestData
  Scenario: Création/liste des types de règlements
    Given I am logged in as admin and on the Administration
    When I follow "Configuration"
    Then the ".content h2" element should contain "Configuration"
    When I follow "Modes de réglements"
    When I follow "Ajouter"
    Then the ".content h2" element should contain "Ajouter un type de règlement"
    When I fill in "payment[name]" with "Un super règlement"
    And I press "Ajouter"
    Then the ".content .message" element should contain "Le type de règlement a été ajouté"
    And I should see "Un super règlement"

  @reloadDbWithTestData
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

  @reloadDbWithTestData
  Scenario: Création/liste des règles
    Given I am logged in as admin and on the Administration
    When I follow "Configuration"
    Then the ".content h2" element should contain "Configuration"
    When I follow "Règles"
    When I follow "Ajouter"
    Then the ".content h2" element should contain "Ajouter une règle"
    When I fill in "rule[label]" with "Une nouvelle règle"
    When I fill in "rule[condition]" with "REM INSCRIPTION"
    When I fill in "rule[isCredit]" with "1"
    # CB
    When I fill in "rule[paymentTypeId]" with "2"
    # TVA à 5.5%
    When I fill in "rule[vat]" with "5_5"
    # À determiner
    When I fill in "rule[categoryId]" with "26"
    # Association AFUP
    When I fill in "rule[eventId]" with "27"
    # Justification obligatoire
    When I fill in "rule[attachmentRequired]" with "1"
    And I press "Ajouter"
    Then the ".content .message" element should contain "La règle a été ajoutée"
    And I should see "Une nouvelle règle REM INSCRIPTION"

    When I follow the button of tooltip "Modifier la règle Une nouvelle règle"
    And the "rule[label]" field should contain "Une nouvelle règle"
    And the "rule[condition]" field should contain "REM INSCRIPTION"
    And The "rule[isCredit]" field should have the following selected value "1"
    And The "rule[paymentTypeId]" field should have the following selected value "2"
    And The "rule[vat]" field should have the following selected value "5_5"
    And The "rule[categoryId]" field should have the following selected value "26"
    And The "rule[eventId]" field should have the following selected value "27"
    And The "rule[attachmentRequired]" field should have the following selected value "1"
