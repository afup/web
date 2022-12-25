Feature: Administration - Partie Assemblée Générale Questions

  @reloadDbWithTestData
  Scenario: Accès à la liste des questions
    Given I am logged in as admin and on the Administration
    And I follow "Assemblée générale - votes"
    Then the ".content h2" element should contain "Assemblée générale - votes"
    And I should see "Vote Oui Non Abstention Actions"

  Scenario: Ajouter une question
    Given I am logged in as admin and on the Administration
    And I follow "Assemblée générale - votes"
    When I follow "Ajouter"
    Then the ".content h2" element should contain "Assemblée générale - questions"
    Then I should see "Assemblée générale du 30/10/2021"
    And I fill in "general_meeting_question_form[label]" with "Une super question"
    And I press "Ajouter cette question"
    Then I should see "La question a été ajoutée"

  Scenario: Modifier une question
    Given I am logged in as admin and on the Administration
    And I follow "Assemblée générale - votes"
    When I follow "question-1-edit"
    Then the ".content h2" element should contain "Modifier la question"
    And I fill in "general_meeting_question_form[label]" with "Une super question modifié"
    And I press "Modifier cette question"
    Then I should see "La question a été modifiée"
    And I should see "Une super question modifié"

  Scenario: Supprimer une question
    Given I am logged in as admin and on the Administration
    And I follow "Assemblée générale - votes"
    When I follow "question-1-delete"
    Then the ".content h2" element should contain "Assemblée générale - votes"
    Then I should see "La question a été supprimée"
