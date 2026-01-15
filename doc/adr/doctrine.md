# ADR - Doctrine ORM

Cet ADR spécifie les règles décidées pour l'utilisation de l'ORM Doctrine.

## La langue

Avant toute chose, il a été décidé d'utiliser l'anglais pour les entités, à l'exception de certains sigles peu
traduisibles en français.

Le [glossaire][glossaire] doit être complété avec chaque nouveau mot anglais afin de conserver un lieu central pour
aider à la compréhension des entités et du code en général.

## Entité

Une entité doit déclarer ses propriétés fortement typées et en `public` (au lieu d'avoir des getters/setters).

Par exemple :

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

Un repository doit hériter de la classe de base `\AppBundle\Doctrine\EntityRepository` et implémenter le constructeur
avec l'entité concernée.

Par exemple :

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

[glossaire]: ../glossaire.md
