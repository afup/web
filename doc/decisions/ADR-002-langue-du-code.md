---
Id: ADR-002
Date: 2026-03-01
Statut: Proposé
---

# Langue du code

## Contexte

Il y a deux problématiques avec la codebase à ce jour :

1. Le mélange des langues : certains mots "métier" sont en français, d'autres en anglais
2. Certains mots "métier" anglais ne sont pas de l'anglais correct

## Décision

Les mots "métier" doivent être rédigés dans la langue dudit métier, autrement dit, dans le cas de l'AFUP, la plupart du
temps en français.

| Contexte       | Langue   | Exemples                                                | Justification                                                                                                                                       |
|----------------|----------|---------------------------------------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------|
| Domaine        | Français | `Inscription`, `Facture`, `Adresse`, `StatutCommande`   | Le code doit parler la langue du métier                                                                                                             |
| Infrastructure | Hybride  | `AntenneRepository`, `CommandeAdapter`, `FactureSender` | Les mots métier sont en français puisque le métier de l'asso est en français, et les mots techniques sont en anglais, pas de raison de les traduire |
| Code technique | Anglais  | `FileUploader`, `HttpClient`, `EventDispatcher`         | Les mots du métier de dev sont toujours en anglais, pas de raison de les traduire                                                                   |
| Commentaires   | Français | `// Vérification du numéro de facture`                  | Pour rester raccord avec le code métier                                                                                                             |
| Tests Behat    | Hybride  | `When I press "Sauvegarder"`                            | Les phrases Behat proviennent d'une librairie et sont déjà en anglais, l'interface est en français                                                  |

La logique principale est d'utiliser la langue déjà utilisée à l'oral pour chaque situation.

Par exemple, on ne dit pas "General Meeting" entre bénévoles, mais "Assemblée Générale".
En revanche, on ne dit pas "Dépot" mais "Repository" par habitude dans notre métier.

## Raisons

{Listez les raisons principales qui justifient cette décision}

1. De par la nature francophone de l'AFUP, la majorité du vocabulaire de l'association est en français
2. Le code métier anglais est difficile à naviguer, car il faut presque systématiquement le traduire pour le comprendre
3. Certains mots sont difficiles voir impossible à traduire :
   - General Meeting ou General Assembly ? Les deux sont valides mais lequel choisir ?
   - Comment traduire `Antenne` ou `Super Apéro` et que ça reste compréhensible ?

## Alternatives considérées

1. **Tout en anglais** :
   - ne serait pas raccord avec le vocabulaire utilisé au jour le jour par les bénévoles
   - cette alternative nécessiterait la création d'un glossaire pour éxpliquer chaque mot anglais et sa traduction française
   - cela impliquerait de débattre le choix de la traduction de certains mots
2. **Tout en français** : les mots techniques ont plus de sens à rester en anglais, car c'est la norme dans le métier de dev

## Conséquences

### Positives

- La navigation et la compréhension du le code est plus simple
- Quand un ou une bénévole parle du site ou d'un sujet de l'association, il est plus simple de retrouver le code concerné
- la contribution est facilité car il y a moins de vocabulaire à apprendre

### Négatives

- Les habitudes ont la vie dure, et écrire du code en français amènera toujours son lot de débat
