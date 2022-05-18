Feature: Event pages - CFP - en anglais

  @reloadDbWithTestData
  Scenario: On se crée son profil Speaker
    Given I am on "/event/forum/cfp?_locale=en"
    Then I should see "Oauth login test"
    When I follow "Connect as userGithub1"
    Then I should see "CFP: forum"
    And I should see "My speaker area"
    And I should see "Your speaker profile is empty."
    When I follow "My speaker profile"
    Then I should see "Profile picture"
    Then The "speaker[civility]" field should only contain the follow values '["Mr.", "Mrs."]'
    When I fill in "speaker[firstname]" with "Mon prénom"
    And I fill in "speaker[lastname]" with "Mon nom de famille"
    And I fill in "speaker[email]" with "monemail@provider.fr"
    And I fill in "speaker[biography]" with "Ma biographie"
    And I attach the file "avatar1.png" to "speaker[photoFile]"
    And I press "Save"
    Then I should see "Profile saved."
