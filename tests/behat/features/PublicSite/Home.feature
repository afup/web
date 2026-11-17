Feature: Site Public - Home

  @reloadDbWithTestData
  Scenario: On voit bien toute la page, même le footer
    Given the current date is "2026-01-01"
    And I am on the homepage
    Then I should see "© AFUP 2000-2026"

  @reloadDbWithTestData
  Scenario: Sur la home on a bien un lien vers le dernier article affiché
    Given the current date is "2026-01-01"
    And I am on the homepage

    # Article à la une
    Then I should see "Un article en Markdown"
    And I should see "30 octobre 2025"
    And I should see "Un peu de text avec de la mise en forme"

    # Article dans la liste
    Then I should see "Les vidéos des talks du Forum PHP 2018 sont disponibles"
    And I should see "14/11"
    And I should not see "venez nous voir en octobre au Forum PHP 2019 ou en mai à l'AFUP Day"

    # Affichage de la page de l'article à la une
    When I follow "Lire l'article complet"
    Then I should see "Un article en Markdown"
    And I should see "Lorem ipsum dolor sit amet"

  @reloadDbWithTestData
  Scenario: Affichage des derniers meetups
    Given the current date is "2025-11-01"
    And I am on the homepage
    Then I should see "Le super meetup"
    And I should see "Lyon"
    And I should see "08/11"

  @reloadDbWithTestData
  Scenario: La home est servie sur / avec une canonical vers /
    Given I am on "/"
    Then the response status code should be 200
    And the response should contain "<link rel=\"canonical\" href=\"https://apachephptest:80/\"/>"

  Scenario: L'ancienne URL /home redirige en 301 vers /
    Given I do not follow redirects
    When I am on "/home"
    Then the response status code should be 301
    And the response header "Location" should match "#^(https?://[^/]+)?/$#"

  @reloadDbWithTestData
  Scenario: Bloc des dernières vidéos
    Given I am on the homepage
    Then I should see "Dernières vidéos" in the "#dernieres-videos h2" element
    # Les 6 talks publiés avec vidéo dont le créneau s'est terminé le plus récemment, du plus récent au plus ancien
    And I should see 6 "#dernieres-videos li" elements
    And I should see "REST ou GraphQL ? Exemples illustrés avec Symfony et API Platform" in the "#dernieres-videos li:nth-child(1)" element
    And the "#dernieres-videos li:nth-child(1)" element should contain "href=\"/talks/2-rest-ou-graphql-exemples-illustres-avec-symfony-et-api-platform\""
    And the "#dernieres-videos li:nth-child(1)" element should contain "https://img.youtube.com/vi/QhAToFl_Omo/hqdefault.jpg"
    And I should see "forum" in the "#dernieres-videos li:nth-child(1)" element
    And I should see "Jouons tous ensemble à un petit jeu" in the "#dernieres-videos li:nth-child(2)" element
    And I should see "Les fibers expliquées" in the "#dernieres-videos li:nth-child(3)" element
    And the "#dernieres-videos li:nth-child(3)" element should contain "href=\"/talks/101-les-fibers-expliquees\""
    And I should see "Rencontres vidéo 2019" in the "#dernieres-videos li:nth-child(3)" element
    And I should see "Domain-Driven Design au quotidien" in the "#dernieres-videos li:nth-child(4)" element
    And I should see "Sécuriser une API" in the "#dernieres-videos li:nth-child(5)" element
    And I should see "PHP 8 en production" in the "#dernieres-videos li:nth-child(6)" element
    # Au-delà des 6 plus récents
    And I should not see "Le plus ancien talk en vidéo" in the "#dernieres-videos" element
    # Talks non publiés ou sans vidéo
    And I should not see "Révolutionons PHP" in the "#dernieres-videos" element
    And I should not see "Une clinique filmée" in the "#dernieres-videos" element
    And I should not see "Un talk sans vidéo" in the "#dernieres-videos" element
    And I should not see "Un talk non planifié" in the "#dernieres-videos" element
    And I should not see "Un talk au créneau pas terminé" in the "#dernieres-videos" element
    When I follow "Toutes les vidéos"
    Then the url should match "/talks/$"
