Feature: Site Public - Meta descriptions des gabarits

  @reloadDbWithTestData
  Scenario: Meta description de la liste des entreprises adhérentes
    Given I am on "/profile/company"
    Then the meta description should be "Les entreprises adhérentes à l'AFUP, qui soutiennent l'association et la communauté PHP francophone : présentation, site web et antennes de rattachement."

  @reloadDbWithTestData
  Scenario: Meta description d'une entreprise tirée de sa description
    Given I am on "/profile/company/1-mycorp"
    Then the meta description should be "MyCorp développe des applications PHP sur mesure & accompagne les équipes dans leurs migrations."

  @reloadDbWithTestData
  Scenario: Meta description d'une entreprise sans description
    Given I am on "/profile/company/2-helios-aerospace"
    Then the meta description should be "Helios Aerospace est une entreprise adhérente à l'AFUP et soutient la communauté PHP francophone."
    And the response should contain "<meta name=\"twitter:description\" content=\"Helios Aerospace est une entreprise adhérente à l&#039;AFUP et soutient la communauté PHP francophone.\" />"

  Scenario: Meta description de la liste des meetups
    Given I am on "/meetups/"
    Then the meta description should be "Les meetups organisés par les antennes locales de l'AFUP partout en France : prochains rendez-vous et événements passés de la communauté PHP."

  Scenario: Meta description de la page « Devenir membre »
    Given I am on "/association/devenir-membre"
    Then the meta description should be "Adhérez à l'AFUP en tant que particulier ou entreprise pour soutenir la communauté PHP francophone : Forum PHP, AFUP Day, antennes, mentorat."

  Scenario: Meta description du formulaire d'adhésion particulier
    Given I am on "/association/adherer/particulier"
    Then the meta description should be "Formulaire d'adhésion individuelle à l'AFUP : créez votre compte de membre pour soutenir la communauté PHP francophone."

  Scenario: Meta description du formulaire d'adhésion entreprise
    Given I am on "/association/adherer/entreprise"
    Then the meta description should be "Formulaire d'adhésion à l'AFUP réservé aux entreprises : créez le compte de votre société pour soutenir l'association et la communauté PHP francophone."
