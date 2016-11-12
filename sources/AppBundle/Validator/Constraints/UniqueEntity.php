<?php


namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueEntity
 *
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class UniqueEntity extends Constraint
{
    public $message = 'Another entity exists for this data: {{ data }}';
    public $repository;
    public $fields = array();

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function getRequiredOptions()
    {
        return ['fields', 'repository'];
    }

    public function getDefaultOption()
    {
        return ['fields', 'repository'];
    }
}
