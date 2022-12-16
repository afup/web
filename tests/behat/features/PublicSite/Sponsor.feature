Feature: Site Public - Devenir sponsor

  @reloadDbWithTestData
  Scenario: Depuis /become-sponsor, on est redirigé vers la page de sponsoring du dernier évènement
    Given I go to "/become-sponsor"
    Then the url should match "/event/forum/sponsor/become-sponsor"
    And the ".container h2" element should contain "Devenir sponsor"
