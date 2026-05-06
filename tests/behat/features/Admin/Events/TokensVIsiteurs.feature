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
    Then I should see "Tarif spécial * 2€ TTC"

  @reloadDbWithTestData
  Scenario: Prolonger la validité d'un token visiteur
    Given I am logged in as admin and on the Administration
    And I follow "Tokens visiteurs"
    # Ajout d'un token
    When I fill in "ticket_special_price[price]" with "2"
    And I fill in "ticket_special_price[description]" with "Token visiteur de test"
    And I press "Enregistrer"
    Then I should see "Le token a été enregistré"
    # Prolongation du token
    And I should see tooltip "Prolonger la validité de 3 jours"
    When I follow the button of tooltip "Prolonger la validité de 3 jours"
    Then I should see "La validité du token a été prolongée de 3 jours."
