Feature: Espace membre, Veille de l'AFUP

  @reloadDbWithTestData
  Scenario: Non à jour de cotisation
    Given I am logged-in with the user "userexpire" and the password "userexpire"
    And I follow "Espace membre"
    Then I should see "Espace membre"
    And I follow "Consulter"
    Then I should see "Veille de l'AFUP"
    And I should see "Vous n'êtes pas à jour de votre cotisation."
    And I should see "Vous ne pourrez donc pas recevoir, vous inscrire ni consulter l'historique des veilles de l'AFUP."

  @reloadDbWithTestData
  Scenario: Abonnement à jour je dois voir l'historique
    Given I am logged-in with the user "paul" and the password "paul"
    And I follow "Espace membre"
    Then I should see "Espace membre"
    And I follow "Gérer mon abonnement"
    Then I should see "Historique des veilles"

  @reloadDbWithTestData
  Scenario: Abonnement / désabonnement
    Given I am logged-in with the user "paul" and the password "paul"
    And I follow "Espace membre"
    Then I should see "Espace membre"
    And I follow "Gérer mon abonnement"
    Then I should see "Veille de l'AFUP"
    And I should see "Vous ne recevez actuellement pas la veille de l'AFUP."
    # Abonnement
    Then I press "M'abonner"
    And I should see "Vous êtes maintenant abonné à la veille de l'AFUP"
    And I should see "Vous recevez actuellement la veille de l'AFUP deux fois par mois sur l'adresse paul.personne@mycorp.fr."
    # Désabonnment
    Then I follow "Me désabonner"
    And I should see "Vous êtes maintenant désabonné à la veille de l'AFUP"
