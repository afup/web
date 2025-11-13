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

    And the "tr[data-qa='antenne-nantes']" element should not contain "youtube.com"

    And the "tr[data-qa='antenne-montpellier']" element should contain "Active"
    And the "tr[data-qa='antenne-clermont']" element should contain "Inactive"
