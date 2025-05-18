Feature: Administration - Partie Assemblée Générale Questions

  Scenario: Un membre ne peut pas accéder aux questions de l'assemblée générale
    Given I am logged-in with the user "paul" and the password "paul"
    And I am on "/admin/members/general_meeting_vote"
    Then the response status code should be 403

  @reloadDbWithTestData
  Scenario: Accès à la liste des questions
    Given I am logged in as admin and on the Administration
    And I follow "afup-main-menu-item--assemblee_generale_votes"
    Then the ".content h2" element should contain "Assemblée générale - votes"
    And I should see "Vote Oui Non Abstention Actions"

  Scenario: Ajouter une question
    Given I am logged in as admin and on the Administration
    And I follow "afup-main-menu-item--assemblee_generale_votes"
    When I follow "Ajouter"
    Then the ".content h2" element should contain "Assemblée générale - questions"
    And I fill in "general_meeting_question_form[label]" with "Une super question"
    And I press "Ajouter cette question"
    Then I should see "La question a été ajoutée"

  Scenario: Modifier une question
    Given I am logged in as admin and on the Administration
    And I follow "afup-main-menu-item--assemblee_generale_votes"
    When I follow "question-1-edit"
    Then the ".content h2" element should contain "Modifier la question"
    And I fill in "general_meeting_question_form[label]" with "Une super question modifié"
    And I press "Modifier cette question"
    Then I should see "La question a été modifiée"
    And I should see "Une super question modifié"

  Scenario: Supprimer une question
    Given I am logged in as admin and on the Administration
    And I follow "afup-main-menu-item--assemblee_generale_votes"
    When I follow "question-1-delete"
    Then the ".content h2" element should contain "Assemblée générale - votes"
    Then I should see "La question a été supprimée"
