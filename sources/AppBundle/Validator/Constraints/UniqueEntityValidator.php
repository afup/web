<?php

declare(strict_types=1);

namespace AppBundle\Validator\Constraints;

use AppBundle\Model\ModelWithUniqueId;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueEntityValidator extends ConstraintValidator
{
    public function __construct(private readonly RepositoryFactory $repositoryFactory) {}

    /**
     * @param ModelWithUniqueId $entity
     */
    public function validate(mixed $entity, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueEntity) {
            throw new UnexpectedTypeException($constraint, UniqueEntity::class);
        }

        if (!$entity instanceof ModelWithUniqueId) {
            throw new UnexpectedTypeException($entity, ModelWithUniqueId::class);
        }

        $repository = $this->repositoryFactory->get($constraint->repository);

        $fields = $constraint->fields;
        $criteria = [];
        foreach ($fields as $field) {
            $propertyName    = 'get' . $field;
            $criteria[$field] = $entity->$propertyName();
        }

        $myEntity = $repository->getOneBy($criteria);

        if ($myEntity !== null && !$myEntity instanceof ModelWithUniqueId) {
            throw new UnexpectedTypeException($myEntity, ModelWithUniqueId::class);
        }

        if ($myEntity !== null && $myEntity->getUniqueId() !== $entity->getUniqueId()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ data }}', implode(', ', $criteria))
                ->addViolation();
        }
    }
}
