Feature: Administration - Évènements - Tokens Visiteurs

  Scenario: Un membre ne peut pas accéder aux tokens visiteurs
    Given I am logged-in with the user "paul" and the password "paul"
    And I am on "/admin/event/special_price"
    Then the response status code should be 403

  @reloadDbWithTestData
  Scenario: Création d'un token visiteurs
    Given I am logged in as admin and on the Administration
    And I follow "Tokens visiteurs"
    Then the ".content h2" element should contain "Tokens visiteurs"
    # Ajout d'un token
    When I fill in "ticket_special_price[price]" with "2"
    And I fill in "ticket_special_price[description]" with "Token visiteur de test"
    And I press "Enregistrer"
    # Listing des tokens
    Then I should see "Le token a été enregistré"
    Then the ".content h2" element should contain "Tokens visiteurs"
    Then the ".content table" element should contain "Token visiteur de test"
    # Lien d'un token
    And I follow "Lien"
    Then I should see "Spécial Forum * 2€ TTC"
