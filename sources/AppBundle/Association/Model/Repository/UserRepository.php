<?php


namespace AppBundle\Association\Model\Repository;

use AppBundle\Association\Model\User;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\Json;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserRepository extends Repository implements MetadataInitializer, UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        $user = $this->getOneBy(['username' => $username]);
        if ($user === null) {
            throw new UsernameNotFoundException(sprintf('Could not find the user "%s"', $username));
        }
        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if ($this->supportsClass(get_class($user)) === false) {
            throw new UnsupportedUserException(sprintf('Instance of %s not supported', get_class($user)));
        }
        return $this->get($user->getId());
    }

    public function supportsClass($class)
    {
        return $class === User::class;
    }

    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(User::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_personnes_physiques');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'id_personne_morale',
                'fieldName' => 'companyId',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'login',
                'fieldName' => 'username',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'mot_de_passe',
                'fieldName' => 'password',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'prenom',
                'fieldName' => 'firstName',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'prenom',
                'fieldName' => 'firstName',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'nom',
                'fieldName' => 'lastName',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'email',
                'fieldName' => 'email',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'niveau',
                'fieldName' => 'level',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'niveau_modules',
                'fieldName' => 'levelModules',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'roles',
                'fieldName' => 'roles',
                'type' => 'json',
                'serializer_options' => [
                    'unserialize' => ['assoc' => true]
                ]
            ])
            ->addField([
                'columnName' => 'adresse',
                'fieldName' => 'address',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'code_postal',
                'fieldName' => 'zipCode',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'ville',
                'fieldName' => 'city',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'id_pays',
                'fieldName' => 'country',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'telephone_fixe',
                'fieldName' => 'phone',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'etat',
                'fieldName' => 'status',
                'type' => 'int'
            ])
        ;

        return $metadata;
    }
}
