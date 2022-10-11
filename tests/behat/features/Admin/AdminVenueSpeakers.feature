Feature: Administration - Partie Venue Speakers

  @reloadDbWithTestData
  Scenario: Liste des speakers liés à un évènement et modification d'une information sur la page info speaker
    Given I am logged in as admin and on the Administration
    And I follow "Venue speakers"
    Then the ".content h2" element should contain "Venue speakers"
    And the ".content table" element should contain "Geoffrey BACHELET"
    # Accès a la page speaker via le bouton associé
    # (ça nous amène à la page /admin/event/speaker-infos?speaker_id=1&id=1)
    When I follow the button of tooltip "Voir sa page"
    Then I should see "Ma page speaker : forum"
    And I should see "Moyen de contact"
    And I should see "Nous vous invitons au restaurant"
    And I should see "Nous vous hébergeons"
    When I fill in "speakers_contact[phone_number]" with "0606060607"
    And I press "Enregistrer"
    # Quand on clique sur Enregistrer, on est redirigé vers l'authentification github
    # car le formulaire renvoie vers la partie publique (/event/forum/speaker-infos) qui ne contient aucune conférence ouverte
    # et pas la partie admin (/admin/event/speaker-infos) qui nous permet d'aller sur n'importe quel évènement.
    # Pour palier à ce problème, on navigue manuellement vers la partie admin pour revenir à la bonne page
    And I am on "/admin/event/speaker-infos?speaker_id=1&id=1"
    Then I should see "Informations de contact enregistrées"
