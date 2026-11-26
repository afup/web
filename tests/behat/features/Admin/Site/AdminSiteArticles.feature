Feature: Administration - Partie Site

  Scenario: Ajout d'un article avec le minimum d'info
    Given I am logged in as admin and on the Administration
    And I follow "Articles"
    Then I should see "Liste des articles"
    And I should see "Actualités"
    When I follow "Ajouter"
    Then I should see "Ajouter un article"
    And I fill in "article[titre]" with "Le titre mini"
    And I fill in "article[contenu]" with "Le contenu mini"
    And I fill in "article[raccourci]" with "url-article-mini"
    And I select "Actualités" from "article[rubrique]"
    And I select "9" from "article[position]"
    And I press "Ajouter"
    When I should see "Liste des articles"
    Then the ".content table" element should contain "Le titre mini"

  @reloadDbWithTestData
  Scenario: Ajout/modification/suppression d'un article
    Given I am logged in as admin and on the Administration
    And I follow "Articles"
    Then I should see "Liste des articles"
    And I should see "Actualités"

    # ajout d'un article
    When I follow "Ajouter"
    Then I should see "Ajouter un article"
    And I fill in "article[titre]" with "Le titre de l'article"
    And I fill in "article[chapeau]" with "Le chapeau de l'article"
    And I fill in "article[contenu]" with "Le contenu de l'article"
    And I fill in "article[description]" with "La description de l'article"
    And I fill in "article[raccourci]" with "url-article"
    And I select "Actualités" from "article[rubrique]"
    And I select "9" from "article[position]"
    And I select "En ligne" from "article[etat]"
    And I select "Associatif" from "article[theme]"
    And I select "forum" from "article[idEvent]"
    And I press "Ajouter"
    When I should see "Liste des articles"
    Then the ".content table" element should contain "Le titre de l'article"
    # vérification de l'article sur le site publique
    When I go to "/news/17-url-article"
    Then I should see "Le titre de l'article"
    Then I should see "Le chapeau de l'article"
    Then I should see "Le contenu de l'article"
    And the meta description should be "La description de l'article"
    And the response should contain "<meta property=\"og:description\" content=\"La description de l&#039;article\" />"
    And the response should contain "<meta name=\"twitter:description\" content=\"La description de l&#039;article\">"

    # modification d'un article
    When I follow "Administration"
    And I follow "Articles"
    Then I follow "modifier_2"
    And I should see "Modifier un article"
    And I fill in "article[titre]" with "Le titre de l'article modifié"
    And I press "Modifier"
    Then I should see "L'article Le titre de l'article modifié a été modifié"
    And I should see "Modifier un article"
    And the "article[titre]" field should contain "Le titre de l'article modifié"
    When I follow "Articles"
    Then the ".content table" element should contain "Le titre de l'article modifié"

    # vérification que le raccourci n'a pas changé avec le titre
    When I go to "/news/17-url-article"
    Then I should see "Le titre de l'article"
    Then I should see "Le chapeau de l'article"
    Then I should see "Le contenu de l'article"

    # suppression d'un article
    When I follow "Administration"
    When I follow "Articles"
    And I follow "supprimer_2"
    Then I should see "Liste des articles"
    But the ".content table" element should not contain "Le titre de l'article modifié"

  Scenario: Le raccourci ne doit pas contenir d'espace
    Given I am logged in as admin and on the Administration
    And I follow "Articles"
    Then I should see "Liste des articles"
    When I follow "Ajouter"
    And I fill in "article[raccourci]" with "un mauvais raccourci"
    And I press "Ajouter"
    Then I should see "Ne doit pas contenir d'espace"

  Scenario: Ajout d'un article avec des émojis
    Given I am logged in as admin and on the Administration
    And I follow "Articles"
    When I follow "Ajouter"
    And I fill in "article[titre]" with "Le titre qui envoi 🚀"
    And I fill in "article[contenu]" with "Pour un contenu rigolo 🤣"
    And I fill in "article[raccourci]" with "url-article-emojis"
    And I select "Actualités" from "article[rubrique]"
    And I select "9" from "article[position]"
    And I press "Ajouter"
    When I should see "Liste des articles"
    Then the ".content table" element should contain "Le titre qui envoi 🚀"

  @reloadDbWithTestData
  Scenario: Ajout d'un article en markdown
    Given I am logged in as admin and on the Administration
    And I follow "Articles"
    Then I should see "Liste des articles"
    And I should see "Actualités"

    # Ajouter l'article
    When I follow "Ajouter"
    Then I should see "Ajouter un article"
    And I fill in "article[titre]" with "Le titre markdown"
    And I fill in "article[chapeau]" with "Le *chapeau* markdown"
    And I fill in "article[contenu]" with "Un peu de **contenu** avec de la *mise* en page et un [lien](https://afup.org)."
    And I fill in "article[raccourci]" with "url-article-markdown"
    And I select "Actualités" from "article[rubrique]"
    And I select "9" from "article[position]"
    And I press "Ajouter"

    # Article dans la liste
    When I should see "Liste des articles"
    Then the ".content table" element should contain "Le titre markdown"
    And I follow "Articles"

    # Vérification du markdown inchangé dans le formulaire
    Then I follow "modifier_17"
    And I should see "Modifier un article"
    And the "article[titre]" field should contain "Le titre markdown"
    And the "article[chapeau]" field should contain "Le *chapeau* markdown"
    And the "article[contenu]" field should contain "Un peu de **contenu** avec de la *mise* en page et un [lien](https://afup.org)."

  @reloadDbWithTestData
  Scenario: Génération du raccourci d'un article
    Given I am logged in as admin and on the Administration
    And I follow "Articles"
    Then I should see "Liste des articles"
    And I should see "Actualités"

    # ajout d'un article
    When I follow "Ajouter"
    Then I should see "Ajouter un article"
    And I fill in "article[titre]" with "Mon super 123 article !"
    And I fill in "article[chapeau]" with "Le chapeau de l'article"
    And I fill in "article[contenu]" with "Le contenu de l'article"
    And I select "Actualités" from "article[rubrique]"
    And I select "9" from "article[position]"
    And I select "En ligne" from "article[etat]"
    And I select "Associatif" from "article[theme]"
    And I select "forum" from "article[idEvent]"
    And I press "Ajouter"
    When I should see "Liste des articles"
    Then the ".content table" element should contain "Mon super 123 article !"

    # vérification de l'article sur le site publique
    When I go to "/news/17-mon-super-123-article"
    Then I should see "Mon super 123 article !"

  @reloadDbWithTestData
  Scenario: Ajout puis suppression de l'image de couverture d'un article
    Given I am logged in as admin and on the Administration
    And I follow "Articles"
    Then I should see "Liste des articles"

    # ajout d'un article avec une image de couverture
    When I follow "Ajouter"
    Then I should see "Ajouter un article"
    And I fill in "article[titre]" with "Un article illustré"
    And I fill in "article[contenu]" with "Le contenu de l'article illustré"
    And I fill in "article[raccourci]" with "url-article-illustre"
    And I select "Actualités" from "article[rubrique]"
    And I select "9" from "article[position]"
    And I select "En ligne" from "article[etat]"
    And I attach the file "avatar1.png" to "article[image]"
    And I press "Ajouter"
    Then I should see "Liste des articles"

    # l'image est affichée sur la page publique
    When I go to "/news/17-url-article-illustre"
    Then I should see "Un article illustré"
    And I should see a "article main figure img" element

    # l'image existante est affichée dans le formulaire d'administration
    When I follow "Administration"
    And I follow "Articles"
    Then I follow "modifier_17"
    And I should see "Modifier un article"
    And I should see "Supprimer l'image de couverture"
    And I should see a "#article-image-preview" element

    # Modifier l'article sans changer l'image la conserve
    And I press "Modifier"
    Then I should see "L'article Un article illustré a été modifié"
    And I should see "Modifier un article"
    And I should see "Supprimer l'image de couverture"
    And I should see a "#article-image-preview" element

    # suppression de l'image de couverture
    When I check "article[supprimerImage]"
    And I press "Modifier"
    Then I should see "L'article Un article illustré a été modifié"
    And I should see "Modifier un article"
    Then I should not see "Supprimer l'image de couverture"
    And I should not see a "#article_supprimerImage" element

    # l'image n'est plus affichée sur la page publique
    When I go to "/news/17-url-article-illustre"
    Then I should not see a "article main figure img" element

  @reloadDbWithTestData
  Scenario: Description d'une page CMS, avec repli sur le début du contenu
    Given I am logged in as admin and on the Administration
    And I follow "Articles"
    When I follow "Ajouter"
    Then I should see "Ajouter un article"
    And I fill in "article[titre]" with "Les statuts"
    And I fill in "article[contenu]" with "Les **statuts** de l'association, adoptés en assemblée générale."
    And I fill in "article[description]" with "Les statuts de l'AFUP & leur historique."
    And I fill in "article[raccourci]" with "statuts"
    And I select "Association" from "article[rubrique]"
    And I select "En ligne" from "article[etat]"
    And I press "Ajouter"
    Then I should see "Liste des articles"

    # la description saisie est la meta description de la page
    When I go to "/p/17-statuts"
    Then I should see "Les statuts"
    And the meta description should be "Les statuts de l'AFUP & leur historique."

    # la description est éditable
    When I follow "Administration"
    And I follow "Articles"
    And I follow "modifier_17"
    Then the "article[description]" field should contain "Les statuts de l'AFUP & leur historique."
    When I fill in "article[description]" with ""
    And I press "Modifier"
    Then I should see "L'article Les statuts a été modifié"

    # sans description, la meta description reprend le début du contenu, sans Markdown
    When I go to "/p/17-statuts"
    Then the meta description should be "Les statuts de l'association, adoptés en assemblée générale."
