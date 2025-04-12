<?php

declare(strict_types=1);


namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\GithubUser;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GithubUserRepository extends Repository implements MetadataInitializer, UserProviderInterface
{
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = []): Metadata
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(GithubUser::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_user_github');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary' => true,
                'autoincrement' => true,
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'github_id',
                'fieldName' => 'githubId',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'login',
                'fieldName' => 'login',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'name',
                'fieldName' => 'name',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'company',
                'fieldName' => 'company',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'profile_url',
                'fieldName' => 'profileUrl',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'avatar_url',
                'fieldName' => 'avatarUrl',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'afup_crew',
                'fieldName' => 'afupCrew',
                'type' => 'bool',
                'serializer' => Boolean::class
            ]);

        return $metadata;
    }

    public function getAllOrderedByLogin()
    {
        $sql = "
            SELECT u.*
            FROM afup_user_github u
            ORDER BY u.login ASC
        ";

        $query = $this->getQuery($sql);

        return $query->query($this->getCollection(new HydratorSingleObject()));
    }

    public function loadUserByIdentifier(string $identifier): GithubUser
    {
        return $this->loadUserByUsername($identifier);
    }

    public function loadUserByUsername(string $username): GithubUser
    {
        $user = $this->getOneBy(['login' => $username]);
        if ($user === null) {
            throw new UserNotFoundException();
        }
        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (($user instanceof GithubUser) === false) {
            throw new UnsupportedUserException();
        }
        $refreshUser = $this->getOneBy(['id' => $user->getId()]);
        if ($refreshUser === null) {
            throw new UserNotFoundException();
        }
        return $refreshUser;
    }

    public function supportsClass(string $class): bool
    {
        return $class === GithubUser::class;
    }
}
