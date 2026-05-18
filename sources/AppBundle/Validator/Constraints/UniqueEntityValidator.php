<?php

declare(strict_types=1);

namespace AppBundle\Validator\Constraints;

use AppBundle\Model\HasUniqueId;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use LogicException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueEntityValidator extends ConstraintValidator
{
    public function __construct(private readonly RepositoryFactory $repositoryFactory) {}

    /**
     * @inheritDoc
     */
    public function validate($entity, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueEntity) {
            throw new UnexpectedTypeException($constraint, UniqueEntity::class);
        }

        if (!$entity instanceof HasUniqueId) {
            throw new LogicException(sprintf('Le modèle %s doit implémenter %s', $entity::class, HasUniqueId::class));
        }

        $repository = $this->repositoryFactory->get($constraint->repository);

        $fields = $constraint->fields;
        $criteria = [];
        foreach ($fields as $field) {
            $propertyName    = 'get' . $field;
            $criteria[$field] = $entity->$propertyName();
        }

        $myEntity = $repository->getOneBy($criteria);
        if (!$myEntity instanceof HasUniqueId) {
            return;
        }

        if ($myEntity->uniqueId() !== $entity->uniqueId()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ data }}', implode(', ', $criteria))
                ->addViolation();
        }
    }
}
