Feature: Administration - Partie Antennes

  @reloadDbWithTestData
  Scenario: Liste des antennes
    Given I am logged in as admin and on the Administration
    And I follow "afup-main-menu-item--antennes_index"
    Then the ".content h2" element should contain "Antennes"

    And the "tr[data-qa='antenne-marseille']" element should contain "Aix-Marseille"
    And the "tr[data-qa='antenne-bordeaux']" element should contain "meetup.com"
    And the "tr[data-qa='antenne-lille']" element should contain "linkedin.com"
    And the "tr[data-qa='antenne-limoges']" element should contain "limoges.afup.org"
    And the "tr[data-qa='antenne-lorraine']" element should contain "bsky.app"
    And the "tr[data-qa='antenne-lyon']" element should contain "youtube.com"
    And the "tr[data-qa='antenne-luxembourg']" element should contain "twitter.com"

    And the "tr[data-qa='antenne-nantes']" element should contain "youtube.com"

    And the "tr[data-qa='antenne-montpellier']" element should contain "Active"
    And the "tr[data-qa='antenne-clermont']" element should contain "Inactive"

  @reloadDbWithTestData
  Scenario: Ajout d'une antenne
    Given I am logged in as admin and on the Administration
    And I follow "afup-main-menu-item--antennes_index"
    When I follow "Ajouter une antenne"
    Then I should see "Ajouter une antenne"
    When I fill in "antenne[code]" with "grenoble"
    And I fill in "antenne[libelle]" with "Grenoble"
    And I fill in "antenne[urlLogo]" with "https://identity.afup.org/logos/grenoble.svg"
    And I fill in "antenne[meetupNomUrl]" with "afup-grenoble-php"
    And I fill in "antenne[meetupIdExterne]" with "12345678"
    And I fill in "antenne[reseauxSociaux][linkedin]" with "afup-grenoble"
    And I fill in "antenne[region]" with "Auvergne-Rhône-Alpes"
    And I fill in "antenne[departements]" with "38"
    And I check "antenne[carte][afficherSurCarte]"
    And I select "left" from "antenne[carte][ancrageLegende]"
    And I fill in "antenne[carte][premiereVille][premierPointX]" with "600"
    And I fill in "antenne[carte][premiereVille][premierPointY]" with "430"
    And I fill in "antenne[carte][premiereVille][deuxiemePointX]" with "680"
    And I fill in "antenne[carte][premiereVille][deuxiemePointY]" with "460"
    And I fill in "antenne[carte][premiereVille][troisiemePointX]" with "710"
    And I fill in "antenne[carte][premiereVille][troisiemePointY]" with "460"
    And I fill in "antenne[carte][premiereVille][latitude]" with "45.188529"
    And I fill in "antenne[carte][premiereVille][longitude]" with "5.724524"
    And I press "Ajouter"
    Then I should see "L'antenne Grenoble a été ajoutée"
    And the "tr[data-qa='antenne-grenoble']" element should contain "Grenoble"
    And the "tr[data-qa='antenne-grenoble']" element should contain "meetup.com"
    And the "tr[data-qa='antenne-grenoble']" element should contain "linkedin.com"
    And the "tr[data-qa='antenne-grenoble']" element should contain "Active"
    When I follow "antenne-edit-grenoble"
    Then the "antenne[departements]" field should contain "38"
    And the "antenne[carte][premiereVille][latitude]" field should contain "45.188529"
    # un logo saisi en URL complète est renvoyé tel quel par l'API
    When I go to "/api/antennes/grenoble"
    Then the response status code should be 200
    And the json response has the key "$.logo.simple" with value "https://identity.afup.org/logos/grenoble.svg"

  @reloadDbWithTestData
  Scenario: Modification d'une antenne
    Given I am logged in as admin and on the Administration
    And I follow "afup-main-menu-item--antennes_index"
    When I follow "antenne-edit-lyon"
    Then I should see "Modifier l'antenne Lyon"
    And I should not see an "#antenne_code" element
    And the "antenne[reseauxSociaux][linkedin]" field should contain "afup-lyon"
    When I fill in "antenne[libelle]" with "Lyon et environs"
    And I fill in "antenne[reseauxSociaux][linkedin]" with "afup-lyon-officiel"
    And I fill in "antenne[slackMembresIdCanal]" with " C0123ABCDEF "
    And I press "Modifier"
    Then I should see "L'antenne Lyon et environs a été modifiée"
    And the "tr[data-qa='antenne-lyon']" element should contain "Lyon et environs"
    And the "tr[data-qa='antenne-lyon']" element should contain "linkedin.com/company/afup-lyon-officiel"
    And the "tr[data-qa='antenne-lyon']" element should contain "youtube.com"
    When I follow "antenne-edit-lyon"
    Then the "antenne[slackMembresIdCanal]" field should contain "C0123ABCDEF"
    # la carte publique est générée à l'affichage : la modification y est visible immédiatement
    When I go to "/association/antennes"
    Then the response should contain "aria-label=\"Lyon et environs\""

  @reloadDbWithTestData
  Scenario: Désactivation d'une antenne
    Given I am logged in as admin and on the Administration
    And I follow "afup-main-menu-item--antennes_index"
    And the "tr[data-qa='antenne-montpellier']" element should contain "Active"
    When I go to "/association/antennes"
    Then the response should contain "aria-label=\"Montpellier\""
    When I go to "/admin/antennes/"
    And I follow "antenne-edit-montpellier"
    And I uncheck "antenne[actif]"
    And I press "Modifier"
    Then I should see "L'antenne Montpellier a été modifiée"
    And the "tr[data-qa='antenne-montpellier']" element should contain "Inactive"
    # une antenne inactive disparaît de la carte publique
    When I go to "/association/antennes"
    Then the response should not contain "aria-label=\"Montpellier\""

  Scenario: Le code d'une nouvelle antenne doit être unique
    Given I am logged in as admin and on the Administration
    And I am on "/admin/antennes/add"
    When I fill in "antenne[code]" with "lyon"
    And I fill in "antenne[libelle]" with "Lyon bis"
    And I fill in "antenne[urlLogo]" with "https://identity.afup.org/logos/lyon.svg"
    And I press "Ajouter"
    Then I should see "Une antenne existe déjà avec le code « lyon »."

  Scenario: Le logo doit être une URL valide
    Given I am logged in as admin and on the Administration
    And I am on "/admin/antennes/add"
    When I fill in "antenne[code]" with "grenoble"
    And I fill in "antenne[libelle]" with "Grenoble"
    And I fill in "antenne[urlLogo]" with "pas-une-url"
    And I press "Ajouter"
    Then I should see "Le logo doit être une URL valide"

  Scenario: Le code et le Meetup doivent être utilisables par l'indexation des meetups
    Given I am logged in as admin and on the Administration
    And I am on "/admin/antennes/add"
    When I fill in "antenne[code]" with "aix-marseille"
    And I fill in "antenne[libelle]" with "Aix-Marseille"
    And I fill in "antenne[urlLogo]" with "https://identity.afup.org/logos/marseille.svg"
    And I fill in "antenne[meetupNomUrl]" with "https://www.meetup.com/afup-aix-marseille-php/"
    And I fill in "antenne[meetupIdExterne]" with "afup-aix-marseille-php"
    And I press "Ajouter"
    Then I should see "Le code doit commencer par une lettre minuscule"
    And I should see "Le nom d'URL Meetup ne doit contenir que le nom du groupe"
    And I should see "L'identifiant Meetup doit être numérique."

  Scenario: Le canal Slack Membres doit être renseigné par son identifiant
    Given I am logged in as admin and on the Administration
    And I am on "/admin/antennes/edit/lyon"
    When I fill in "antenne[slackMembresIdCanal]" with "#afup-lyon"
    And I press "Modifier"
    Then I should see "L'identifiant du canal Slack doit être un ID de canal"

  Scenario: Un lecteur des antennes ne peut pas les modifier
    Given I am logged-in with the user "lecteurantennes" and the password "lecteurantennes"
    When I go to "/admin/antennes/"
    Then the response status code should be 200
    And the "tr[data-qa='antenne-lyon']" element should contain "Lyon"
    And I should not see "Ajouter une antenne"
    And I should not see an "[data-qa='antenne-edit-lyon']" element
    When I go to "/admin/antennes/add"
    Then the response status code should be 403
    When I go to "/admin/antennes/edit/lyon"
    Then the response status code should be 403
