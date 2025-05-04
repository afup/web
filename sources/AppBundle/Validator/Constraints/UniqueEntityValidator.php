<?php

declare(strict_types=1);

namespace AppBundle\Validator\Constraints;

use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueEntityValidator extends ConstraintValidator
{
    public function __construct(private readonly RepositoryFactory $repositoryFactory)
    {
    }

    /**
     * @inheritDoc
     */
    public function validate($entity, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueEntity) {
            throw new UnexpectedTypeException($constraint, UniqueEntity::class);
        }
        $repository = $this->repositoryFactory->get($constraint->repository);

        $fields = $constraint->fields;
        $criteria = [];
        foreach ($fields as $field) {
            $propertyName    = 'get' . $field;
            $criteria[$field] = $entity->$propertyName();
        }

        $myEntity = $repository->getOneBy($criteria);

        if ($myEntity !== null && $myEntity->getId() !== $entity->getId()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ data }}', implode(', ', $criteria))
                ->addViolation();
        }
    }
}
