---
Id: ADR-003
Date: 2026-06-29
Statut: Proposé
---

# Les entités Doctrine héritent d'une classe parente

## Contexte

Avec l'ajout de la baseline PHPStan et le passage au niveau 10, il y a pas mal d'endroits dans le code où l'id nullable
des entités pose problème.

Par exemple, quand on récupère une liste d'entités depuis un repository, on sait que l'id est présent, mais pas PHPStan
car la propriété reste nullable dans la classe de l'entité.

Cela force des vérifications qui n'apportent pas grand chose et rendent le code plus difficile à lire et naviguer.

Par exemple :

```php
#[ORM\Entity]
#[ORM\Table(name: 'exemple')]
class Entity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    public string $foo = null;
}

class ExampleRepository
{
    /**
     * @return array<Entity>
     */
    public function all(): array { /* return ... */ }
}

$entities = $exempleRepository->all();

$map = [];
foreach ($entities as $entity) {
    // Cette ligne va déclencher une erreur PHPStan car l'id pourrait être nullable,
    // alors qu'on sait ici que ce n'est pas le cas.
    $map[$entity->id] = $entity->foo;
    
    // Il faudrait faire ça à chaque fois :
    if ($entity->id === null) {
        continue;
    }
    
    $map[$entity->id] = $entity->foo;
}
```

## Décision

Les entités Doctrine héritent d'une classe abstraite contenant l'id et une méthode pour vérifier sa présence.

Cela permet à PHPStan de mieux analyser le code, tout en conservant une certaine sécurité. Si une entité n'est pas
persistée et qu'on tente de lire son id, cela déclenche une erreur.

### Détails d'implémentation

```php
use AppBundle\Doctrine\Entity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'exemple')]
class Exemple extends Entity
{
    #[ORM\Column(length: 255, nullable: false)]
    public string $nonNullbale;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $nullable = null;
}
```

Et à l'utilisation :

```php
if ($exemple->isPersisted()) {
    // $exemple->id est initialisé et non-null
}
```

## Alternatives considérées

1. **Un trait** : C'est plus difficile et lent à analyser pour PHPStan qu'une classe parente.
2. **Vérifier l'id à chaque fois** : Le code devient moins lisible pour peu d'intérêt.

## Conséquences

### Positives

Quand on récupère une ou plusieurs entités depuis la base de données, plus besoin de vérifier la présence de l'id dans
l'instance.

Si on tente d'accéder à l'id d'une entité à un endroit non vérifié, une erreur survient fort et au bon endroit (au lieu
de trimballer un `null` plus loin dans le code).

### Négatives

Toutes les entités doivent hériter d'une classe parente.

Cela ne fonctionne qu'avec des entités qui ont un id entier auto-incrément.

## Références

Analyse des traits par PHPStan : https://phpstan.org/blog/how-phpstan-analyses-traits

Exemple PHPStan : https://phpstan.org/r/3f3354d7-d6f5-4493-bfbd-3f7a37cf8d32
