Feature: Administration - Trésorerie - Devis/Facture

  @reloadDbWithTestData
  @clearEmails
  Scenario: Créer/Modifier un devis, une facture
    Given I am logged in as admin and on the Administration
    When I follow "Devis"
    Then the ".content h2" element should contain "Liste devis"
    And I follow "Ajouter"
    # Création du devis
    When I fill in "societe" with "ESN dev en folie"
    And I fill in "service" with "Développement"
    And I fill in "adresse" with "1 rue du chemin"
    And I fill in "code_postal" with "75000"
    And I fill in "ville" with "Paris"
    And I fill in "nom" with "Moreau"
    And I fill in "prenom" with "Martine"
    And I fill in "tel" with "0101010101"
    And I fill in "email" with "martine@ens-en-folie.biz"
    And I fill in "tva_intra" with "FR7612345"
    And I fill in "ref_clt1" with "CLIENT-AFGD5S"
    And I fill in "ref_clt2" with "AFGD5S"
    And I fill in "ref_clt3" with "000AFGD5S"
    And I fill in "observation" with "Ce devis ne comprend pas les selfies avec l'équipe"
    And I fill in "ref1" with "COACH-001"
    And I fill in "designation1" with "Coaching d'équipe pour l'accompagnement sans douleur à Symfony"
    And I fill in "quantite1" with "12"
    And I fill in "pu1" with "660"
    And I fill in "ref2" with "ARCHI-007"
    And I fill in "designation2" with "Architecture en KKK"
    And I fill in "quantite2" with "1"
    And I fill in "pu2" with "12000"
    When I press "Ajouter"
    Then I should see "L'écriture a été ajoutée"
    And I should see "ESN dev en folie"
    And I should see "Paris"
    And I should see "CLIENT-AFGD5S"
    And I should see "19 920,00"
    # Modification du devis
    When I follow the button of tooltip "Modifier le devis ESN dev en folie"
    And I fill in "ville" with "Paris Cedex 1"
    When I press "Modifier"
    Then I should see "L'écriture a été modifiée"
    And I should see "ESN dev en folie"
    And I should see "Paris Cedex 1"
    And I should see "CLIENT-AFGD5S"
    And I should see "19 920,00"
    # Téléchargement du devis
    And I follow the button of tooltip "Télécharger le devis"
    Then the response header "Content-disposition" should match '#attachment; filename="Devis - ESN dev en folie - (.*).pdf"#'
    When I parse the pdf downloaded content
    Then The page "1" of the PDF should contain "ESN dev en folie"
    # Transformation du devis en facture
    When I go to "/admin/"
    And I follow "Devis"
    Then I follow the button of tooltip "Transférer la ligne ESN dev en folie en facture"
    And I should see "Le devis a été transformé en facture"
    And I should see "ESN dev en folie"
    And I should see "Paris Cedex 1"
    And I should see "En attente"
    # Modification de la facture
    Then I follow the button of tooltip "Modifier la ligne ESN dev en folie"
    And I should see "Modifier une facture"
    Then I fill in "ville" with "Paris Cedex 7"
    When I select "1" from "etat_paiement"
    And I press "Modifier"
    Then I should see "L'écriture a été modifiée"
    And I should see "Paris Cedex 7"
    And I should see "Payé"
    # Envoi de la facture par email
    Then I follow the button of tooltip "Envoyer la facture par mail"
    And I should only receive the following emails:
      | from               | to                         | subject      |
      | <bonjour@afup.org> | <martine@ens-en-folie.biz> | Facture AFUP |
    Then the ".content .message" element should contain "La facture a été envoyée"
    # Lien de paiement
    Then I follow the button of tooltip "Récupérer le lien de paiement en ligne"
    Then I should see "Paiement en ligne de la facture"
    Then I should see "Facture au format PDF"
    # Téléchargement de la facture
    When I go to "/admin/"
    And I follow "Factures"
    And I follow the button of tooltip "Télécharger la facture"
    Then the response header "Content-disposition" should match '#attachment; filename="Facture - ESN dev en folie - (.*).pdf"#'
    When I parse the pdf downloaded content
    Then The page "1" of the PDF should contain "TVA : FR7612345"
