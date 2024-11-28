Feature: Administration - Partie Site

  @reloadDbWithTestData
  Scenario: Ajout/modification/suppression d'une feuille
    Given I am logged in as admin and on the Administration
    And I follow "Feuilles"
    Then I should see "Liste des feuilles"
    And I should see "Accueil"
    # ajout d'une feuille
    When I follow "Ajouter"
    Then I should see "Ajouter une feuille"
    When I fill in "nom" with "Feuille test"
    And I fill in "lien" with "http://lien"
    And I fill in "image_alt" with "Texte image alt"
    And I press "Ajouter"
    Then I should see "Liste des feuilles"
    And the ".content table" element should contain "Feuille test"
    # modification d'une feuille
    When I follow "modifier_1013"
    Then I should see "Modifier une feuille"
    And I should see "Feuille test"
    And the "lien" field should contain "http://lien"
    And the "image_alt" field should contain "Texte image alt"
    When I fill in "nom" with "Feuille modifiée"
    And I press "Modifier"
    Then I should see "Liste des feuilles"
    And the ".content table" element should contain "Feuille modifiée"
    # suppression d'une feuille
    When I follow "Feuilles"
    And I follow "supprimer_1013"
    Then I should see "Liste des feuilles"
    But the ".content table" element should not contain "Feuille test"
