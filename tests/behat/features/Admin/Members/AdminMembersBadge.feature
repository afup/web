Feature: Administration - Partie Badge

  @reloadDbWithTestData
  Scenario: Ajout d'un badge
    Given I am logged in as admin and on the Administration
    And I follow "Badges"
    Then the ".content h2" element should contain "Badges"
    When I follow "Ajouter"
    Then the ".content h2" element should contain "Nouveau badge"
    When I fill in "badge[label]" with "Un super Badge"
    Then print last response
    And I attach the file "badge1.png" to "badge[image]"
    And I press "Créer"
    Then I should see "Le badge a été ajouté"
    And I should see "Un super Badge"
