# Architecture Decision Records (ADR)

## Qu'est-ce qu'un ADR ?

Un ADR est un document décrivant une décision d'architecture pour le projet. Il doit contenir le contexte de la décision
et ses effets sur le projet.

Ce n'est pas toujours nécessaire de créer un ADR, voici quelques exemples de situations où ça peut servir :

- Un refactor conséquent
- L'ajout d'une librairie
- Choisir une architecture particulière
- Choisir un pattern ou une convention

## Comment créer un ADR ?

1. Créer un fichier nommé `ADR-XXX-le-super-titre.md` dans le dossier `doc/decisions`
2. Remplir le fichier en utilisant [le template]
3. Soumettre l'ADR à discussion (en ouvrant une PR et, si besoin, en venant en parler au point mensuel)
4. Une fois que l'ADR est validé, mettre à jour son statut et le commit

Il est recommandé de soumettre un ADR dans la même PR que le code concerné. Si ce n'est pas pertinent, il reste tout à
fait possible de faire une PR dédiée à un ADR.

## Template d'un ADR

```markdown
---
Id: ADR-XXX
Date: Y-m-d
Statut: {Proposé | Accepté | Déprécié | Remplacé par ADR-YYY}
---

# {Titre court et descriptif}

## Contexte

{Décrivez le problème ou la situation qui nécessite une décision}

## Décision

{Décrivez la décision prise de manière claire et concise}

### Détails d'implémentation (optionnel)

{Si nécessaire, ajoutez des détails techniques sur l'implémentation}

## Raisons

{Listez les raisons principales qui justifient cette décision}

1. Raison 1
2. Raison 2
3. ...

## Alternatives considérées

{Listez les autres options envisagées et pourquoi elles n'ont pas été retenues}

1. **Alternative 1** : Pourquoi rejetée
2. **Alternative 2** : Pourquoi rejetée

## Conséquences

### Positives

- Conséquence positive 1
- Conséquence positive 2

### Négatives

- Conséquence négative 1
- Conséquence négative 2

## Références

{Liens vers la documentation, articles, discussions qui ont influencé la décision}
```

## Références

- [Architecture Decision Records](https://adr.github.io/)
- [Documenting Architecture Decisions](https://cognitect.com/blog/2011/11/15/documenting-architecture-decisions)
