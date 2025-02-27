Feature: Site Public - Super Apéro PHP

  @reloadDbWithTestData
  Scenario: On accède à la page Super Apéro PHP
    Given I am on "/super-apero"
    Then the current URL should match "#/association/super-apero$#"
    And the "#main h1" element should contain "Super-apéro PHP"
    And the response should contain "Super Apéro PHP – Édition Spéciale"
    And the response should contain "Super Apéro PHP chez WanadevDigital"
