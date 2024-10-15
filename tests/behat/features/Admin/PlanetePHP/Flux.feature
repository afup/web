Feature: Administration - Planète PHP - Flux

  Scenario: Un membre ne peut pas accéder à la gestion de planète PHP
    Given I am logged-in with the user "paul" and the password "paul"
    And I am on "/admin/planete/feeds"
    Then the response status code should be 403
    And I am on "/admin/planete/feeds/add"
    Then the response status code should be 403
    And I am on "/admin/planete/feeds/edit"
    Then the response status code should be 403
    And I am on "/admin/planete/feeds/delete"
    Then the response status code should be 403

  @reloadDbWithTestData
  Scenario: Gestion des flux
    Given I am logged in as admin and on the Administration
    When I follow "Flux"
    Then the ".content h2" element should contain "Flux"
    # Ajout d'un flux
    When I follow "Ajouter"
    Then the ".content h2" element should contain "Ajouter un flux"
    When I fill in "feed_form[name]" with "Site web les-tilleuls.coop"
    And I fill in "feed_form[url]" with "https://les-tilleuls.coop"
    And I fill in "feed_form[feed]" with "http://statictestresources/feed.xml"
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
