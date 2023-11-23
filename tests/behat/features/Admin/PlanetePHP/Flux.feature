Feature: Administration - Planète PHP - Flux

  Scenario: Gestion des flux
    Given I am logged in as admin and on the Administration
    When I follow "Flux"
    Then the ".content h2" element should contain "Flux"
    # Ajout d'un flux
    When I follow "Ajouter"
    Then the ".content h2" element should contain "Ajouter un flux"
    When I fill in "feed_form[name]" with "Site web les-tilleuls.coop"
    And I fill in "feed_form[url]" with "https://les-tilleuls.coop"
    And I fill in "feed_form[feed]" with "https://les-tilleuls.coop/feed.xml"
    And I press "Ajouter"
    Then the ".content .message" element should contain "Le flux a été ajouté"
    # Liste des flux
    And I should see "les-tilleuls.coop https://les-tilleuls.coop Actif Oui non testé"
    # Test de validité
    When I follow "Test validité"
    And I should see "les-tilleuls.coop https://les-tilleuls.coop Actif Oui validé"
    # Modification + désactivation d'un flux
    When I follow the button of tooltip "Modifier la fiche de Site web les-tilleuls.coop"
    Then the ".content h2" element should contain "Modifier un flux"
    When I fill in "feed_form[name]" with "Site web les-tilleuls.coop modifié"
    And I select "0" from "feed_form[status]"
    And I press "Modifier"
    Then the ".content .message" element should contain "Le flux a été modifié"
    And I should see "les-tilleuls.coop modifié https://les-tilleuls.coop Inactif"
     # Suppression
    When I follow the button of tooltip "Supprimer la fiche de Site web les-tilleuls.coop modifié"
    Then the ".content .message" element should contain "Le flux a été supprimé"
    And I should not see "les-tilleuls.coop"
