Feature: Event pages - Proposition speaker

  @reloadDbWithTestData
  @clearEmails
  Scenario: On envoie une proposition de speaker
    Given I am on "/event/forum"
    When I follow "Suggérer un·e conférencier·e"
    Then I should see "À chaque évènement, le pôle conférences travaille dur pour"
    When I fill in "speaker_suggestion[suggester_email]" with "mailspeaker@yahoo.fr"
    And I fill in "speaker_suggestion[suggester_name]" with "Dark Vador"
    And I fill in "speaker_suggestion[speaker_name]" with "Luke Skywalker"
    And I fill in "speaker_suggestion[comment]" with "Parle très bien des sabres laser"
    And I press "Suggérer"
    Then I should see "Merci pour votre suggestion"
    Then I should only receive the following emails:
      | to                     | subject                                |
      | <conferences@afup.org> | forum - Nouvelle suggestion de speaker |
    And the plain text content of the message of id "1" should be :
"""
Email soumetteur : mailspeaker@yahoo.fr
Nom soumetteur : Dark Vador
Nom speaker : Luke Skywalker
Commentaire :

Parle très bien des sabres laser


"""

