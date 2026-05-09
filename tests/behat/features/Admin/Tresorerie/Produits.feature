Feature: Administration - Trésorerie - Produits

  @reloadDbWithTestData
  Scenario: Création / édition / suppression d'un produit
    Given I am logged in as admin and on the Administration
    When I follow "Produits"
    Then the ".content h2" element should contain "Produits"
    When I follow "Ajouter"
    Then the ".content h2" element should contain "Ajouter un produit"
    And I fill in "produit[reference]" with "forum_php_2026"
    And I fill in "produit[designation]" with "Forum PHP 2026 - Sponsoring Argent sans stand"
    And I fill in "produit[quantite]" with "1"
    And I fill in "produit[prixUnitaireHt]" with "123,45"
    And I select "20" from "produit[tauxTva]"
    And I press "Ajouter"
    Then the ".content .message" element should contain "Le produit a été ajouté"
    And I should see "Forum PHP 2026 - Sponsoring Argent sans stand"
    And I should see "forum_php_2026"

    When I follow the button of tooltip "Modifier la ligne"
    Then the ".content h2" element should contain "Modifier un produit"
    And I fill in "produit[reference]" with "forum_php_2027"
    And I fill in "produit[designation]" with "Forum PHP 2027 - Sponsoring Argent sans stand"
    And I fill in "produit[quantite]" with "2"
    And I fill in "produit[prixUnitaireHt]" with "456"
    And I select "5.5" from "produit[tauxTva]"
    And I press "Modifier"
    Then the ".content .message" element should contain "Le produit a été modifié"
    And I should see "forum_php_2027"
    And I should see "Forum PHP 2027 - Sponsoring Argent sans stand"
    And I should see "2"
    And I should see "456"
    And I should see "5.5 %"

    When I follow the button of tooltip "Supprimer la ligne"
    Then the ".content .message" element should contain "Le produit a été supprimé"
    And I should not see "forum_php_2027"
