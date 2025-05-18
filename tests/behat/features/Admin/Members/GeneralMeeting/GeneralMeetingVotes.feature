Feature: Administration - Partie Assemblée Générale Votes

  @reloadDbWithTestData
  Scenario: Accès à la liste des votes
    Given I am logged in as admin and on the Administration
    And I follow "afup-main-menu-item--assemblee_generale_votes"
    Then the ".content h2" element should contain "Assemblée générale - votes"
    And I should see "Vote Oui Non Abstention Actions"

  Scenario: Ouvrir et fermer un vote
    Given I am logged in as admin and on the Administration
    And I follow "afup-main-menu-item--assemblee_generale_votes"
    When I follow "question-1-open"
    Then the ".content h2" element should contain "Assemblée générale - votes"
    And I should see "Le vote a été ouvert"
    When I follow "question-1-close"
    Then the ".content h2" element should contain "Assemblée générale - votes"
    And I should see "Le vote a été fermé"
