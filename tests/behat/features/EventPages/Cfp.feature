Feature: Event pages - CFP

  @reloadDbWithTestData
  Scenario: On se crée son profil speaker userGithub1
    Given I am on "/event/forum/cfp"
    Then I should see "Oauth login test"
    When I follow "Connect as userGithub1"
    Then I should see "CFP: forum"
    And I should see "Mon espace conférencier"
    And I should see "Vous n'avez pas encore rempli votre profil conférencier."
    When I follow "Mon profil conférencier"
    Then I should see "Photo de profil"
    Then The "speaker[civility]" field should only contain the follow values '["M", "Mme"]'
    When I fill in "speaker[firstname]" with "Mon prénom"
    And I fill in "speaker[lastname]" with "Mon prénom"
    And I fill in "speaker[email]" with "monemail@provider.fr"
    And I fill in "speaker[biography]" with "Ma biographie"
    And I attach the file "avatar1.png" to "speaker[photoFile]"
    And I press "Sauvegarder"
    Then I should see "Profil sauvegardé."

  Scenario: Sans proposition on voit un message
    Given I am on "/event/forum/cfp"
    Then I should see "Oauth login test"
    When I follow "Connect as userGithub2"
    Then I should see "Mon espace conférencier"
    When I follow "Voter pour les conférences"
    Then I should see "Les nouvelles conférences à noter"
    And I should see "Il n'y a plus aucune conférence à noter !"

  Scenario: On crée une nouvelle proposition en tant que userGithub1 mais on ne peut pas la noter
    Given I am on "/event/forum/cfp"
    Then I should see "Oauth login test"
    When I follow "Connect as userGithub1"
    Then I should see "Mon espace conférencier"
    When I follow "Nouvelle proposition"
    Then I should see "J'accepte le code de conduite et les conditions générales de participation"
    When I fill in "talk[title]" with "Généalogie des poissons rouges"
    And I fill in "talk[abstract]" with "L'histoire des poissons rouges à travers les ages"
    And I check "talk[codeOfConduct]"
    And I press "Sauvegarder"
    When I am on "/event/forum/cfp"
    Then I should see "Généalogie des poissons rouges"
    When I am on "/event/forum/vote"
    Then I should not see "Généalogie des poissons rouges"

  Scenario: On voit la nouvelle proposition en tant que userGithub2
    Given I am on "/event/forum/cfp"
    Then I should see "Oauth login test"
    When I follow "Connect as userGithub2"
    Then I should see "Mon espace conférencier"
    When I follow "Voter pour les conférences"
    Then I should see "Les nouvelles conférences à noter"
    And I should see "Généalogie des poissons rouges"
