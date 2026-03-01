---
Id: ADR-001
Date: 2026-03-01
Statut: Proposé
---

# Doctrine ORM

## Contexte

Le projet web de l'AFUP a beaucoup d'historique et a vu passer beaucoup d'évolutions du PHP.

Il y a à ce jour 3 façon d'accéder à la base de données :
- [Ting][ting] : un datamapper léger
- [Doctrine DBAL][doctrine-dbal] : un query builder
- La classe `Base_De_Donnees` : un wrapper autour des fonctions `mysqli_*`

## Décision

Les accès à la base de données se font via l'utilisation de soit [Doctrine DBAL][doctrine-dbal], soit [Doctrine ORM][doctrine-orm].

### Détails d'implémentation (optionnel)

## Entité

Une entité doit déclarer ses propriétés fortement typées et en `public` (au lieu d'avoir des getters/setters).

```php
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'exemple')]
class Exemple
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    public string $nonNullbale;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $nullable = null;
}
```

## Repository

Un repository doit hériter de la classe de base `\AppBundle\Doctrine\EntityRepository` et doit implémenter le
constructeur avec l'entité concernée.

```php
use AppBundle\Exemple\Entity\Exemple;
use AppBundle\Doctrine\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Exemple>
 */
final class ExempleRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exemple::class);
    }
}
```

## Raisons

Cette décision rejoint plusieurs autres liées à la volontée du pôle de rendre le code plus accessible à la contribution
et plus facile à la maintenance.

1. Doctrine est aujourd'hui le standard pour les accès à une base de données en PHP
2. Il y a beaucoup d'opérations CRUD qui sont simplifiées avec Doctrine ORM
3. Il reste possible d'affiner dans certains cas avec Doctrine DBAL

## Conséquences

### Positives

- Utiliser un ORM très populaire permet de faciliter la contribution
- Les formulaires du back-office sont plus simples à rédiger (Symfony se pair bien avec Doctrine ORM)

### Négatives

- Cela implique une période de cohabitation et de refactor de l'ancien code

[ting]: https://packagist.org/packages/ccmbenchmark/ting
[doctrine-dbal]: https://packagist.org/packages/doctrine/dbal
[doctrine-orm]: https://packagist.org/packages/doctrine/orm
