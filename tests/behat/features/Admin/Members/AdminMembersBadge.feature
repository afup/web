Feature: Administration - Partie Badge

  @reloadDbWithTestData
  Scenario: Ajout d'un badge
    Given I am logged in as admin and on the Administration
    And I follow "Badges"
    Then the ".content h2" element should contain "Badges"
    When I follow "Ajouter"
    Then the ".content h2" element should contain "Nouveau badge"
    When I fill in "badge[label]" with "Un super Badge"
    And I attach the file "badge1.png" to "badge[image]"
    And I press "Créer"
    Then I should see "Le badge a été ajouté"
    And I should see "Un super Badge"

  Scenario: Association d'un badge à une personne physique
    Given I am logged in as admin and on the Administration
    And I follow "Personnes physiques"
    Then the ".content h2" element should contain "Personnes physiques"
    # Suit le lien "Modifier la fiche de Personne Paul"
    And I follow "modifier_5"
    # Valide l'ajout du badge
    And I press "user_badge_save"
    Then the "table.ui.table.striped.compact.celled" element should contain "Un super Badge"
