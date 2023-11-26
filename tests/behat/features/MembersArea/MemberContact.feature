Feature: Espace membre > Personne physique > Coordonnées

  @reloadDbWithTestData
  Scenario: Invitation des membres
    Given I am logged-in with the user "paul" and the password "paul"
    When I follow "Espace membre"
    Then I should see "Espace membre"
    And I should see "Antenne la plus proche : Aucune"
    When I follow "Modifier les coordonnées"
    Then I should see "Mes coordonnées"
    When I fill in "contact_details[email]" with "paul.personne2@mycorp.fr"
    And I fill in "contact_details[address]" with "Rue du chemin"
    And I fill in "contact_details[zipcode]" with "75000"
    And I fill in "contact_details[city]" with "Ville"
    And I select "FR" from "contact_details[country]"
    And I select "bordeaux" from "contact_details[nearest_office]"
    When I press "contact_details_save"
    Then I should see "Votre compte a été modifié !"
    When I follow "Accueil"
    And I should see "Antenne la plus proche : Bordeaux"