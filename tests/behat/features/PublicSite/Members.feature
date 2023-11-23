Feature: Site Public - Membres

  @reloadDbWithTestData
  @clearEmails
  Scenario: Liste des entreprises
    Given I am on the homepage
    When I follow "Membres"
    Then I should see "Entreprises adhérentes"
    And I should see "MyCorp"
    When I follow "MyCorp"
    Then I should see "MyCorp"
    Then I should see "L'entreprise"
    Then I should see "MyCorp n'a pas renseigné d'antenne à proximité."
