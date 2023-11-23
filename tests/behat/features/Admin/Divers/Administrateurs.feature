Feature: Administration - Administrateurs du site

  @reloadDbWithTestData
  Scenario: Afficher / modifier les administrateurs du site
    Given I am logged in as admin and on the Administration
    And I follow "Administrateurs du site"
    Then I should see "Administrateurs du site"
    And I should see "Admin Admin Actif Administrateur"
    And I follow the button of tooltip "Modifier la fiche de Admin Admin"
    And I should see "Modifier une personne physique"
    And I fill in "user_edit_lastname" with "SuperLastnameAdmin"
    And I fill in "user_edit_firstname" with "SuperFirstnameAdmin"
    And I fill in "user_edit_address" with "Address"
    And I fill in "user_edit_zipcode" with "77777"
    And I fill in "user_edit_city" with "City"
    And I press "Modifier"
    Then the ".content .message" element should contain "La personne physique a été modifiée"
    And I follow "Administrateurs du site"
    And I should see "SuperLastnameAdmin SuperFirstnameAdmin Actif Administrateur"
