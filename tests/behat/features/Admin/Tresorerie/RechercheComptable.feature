Feature: Administration - Trésorerie - Recherche comptable

  @reloadDbWithTestData
  Scenario: Recherche Comptable pour raoul
    Given I am logged in as admin and on the Administration
    When I follow "Recherche comptable"
    Then the ".content h2" element should contain "Recherche comptable"
    When I fill in "search[query]" with "raoul"
    And I press "Rechercher"
    And the "h2.ui.header" element should contain "Cotisations de personnes morales"
    And I should see "13/07/2018 >"
    And I should see "150.00 Dupont Raoul <raoul.dupont@mycorp.fr>"

  Scenario: Recherche Comptable pour paul
    Given I am logged in as admin and on the Administration
    When I follow "Recherche comptable"
    Then the ".content h2" element should contain "Recherche comptable"
    When I fill in "search[query]" with "paul"
    And I press "Rechercher"
    And the "h2.ui.header" element should contain "Cotisations de personnes physiques"
    And I should see "13/07/2018 >"
    And I should see "25.00 Paul Personne <paul.personne@mycorp.fr> @paul"

  @reloadDbWithTestData
  Scenario: Télécharger une facture Forum depuis la recherche comptable
    Given I am logged in as admin and on the Administration
    When I follow "Recherche comptable"
    Then the ".content h2" element should contain "Recherche comptable"
    When I fill in "search[query]" with "Helios Aerospace"
    And I press "Rechercher"
    Then the "h2.ui.header" element should contain "Factures Forum"
    And the ".content table" element should contain "REF-TEST-001"
    And the ".content table" element should contain "Helios Aerospace"
    When I follow "telecharger_REF-TEST-001"
    Then the response header "Content-disposition" should match '#attachment; filename="Facture - Helios Aerospace - (.*).pdf"#'
