<?php


namespace AppBundle\Association\Model\Repository;

use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\User;
use Aura\SqlQuery\Common\SelectInterface;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserRepository extends Repository implements MetadataInitializer, UserProviderInterface
{
    const USER_TYPE_PHYSICAL = 0;
    const USER_TYPE_COMPANY = 1;
    const USER_TYPE_ALL = 2;

    public function loadUserByUsername($username)
    {
        $queryBuilder = $this->getQueryBuilderWithCompleteUser();
        $queryBuilder
            ->where('app.`login` = :username')
            ->orWhere('app.`email` = :email')
        ;
        $result = $this
            ->getPreparedQuery($queryBuilder->getStatement())
            ->setParams([
                'username' => $username,
                'email' => $username
            ])
            ->query($this->getCollection($this->getHydratorForUser()));

        if ($result->count() === 0) {
            throw new UsernameNotFoundException(sprintf('Could not find the user with login "%s"', $username));
        }

        return $result->first();
    }

    public function loadUserByHash($hash)
    {
        $queryBuilder = $this->getQueryBuilderWithCompleteUser();
        $queryBuilder
            ->having('hash = :hash')
        ;
        $result = $this
            ->getPreparedQuery($queryBuilder->getStatement())
            ->setParams([
                'hash' => $hash
            ])
            ->query($this->getCollection($this->getHydratorForUser()));

        if ($result->count() === 0) {
            throw new UsernameNotFoundException(sprintf('Could not find the user with hash "%s"', $hash));
        }

        return $result->first();
    }

    public function loadActiveUsersByCompany(CompanyMember $companyMember)
    {
        $queryBuilder = $this->getQueryBuilderWithCompleteUser();
        $queryBuilder
            ->where('apm.id = :company')
            ->where('app.etat = :state')
        ;
        return $this
            ->getPreparedQuery($queryBuilder->getStatement())
            ->setParams([
                'company' => $companyMember->getId(),
                'state' => User::STATUS_ACTIVE
            ])
            ->query($this->getCollection($this->getHydratorForUser()))
            ;
    }

    /**
     * @return SelectInterface
     */
    private function getQueryBuilderWithSubscriptions()
    {
        /**
         * @var $queryBuilder SelectInterface
         */
        $queryBuilder = $this->getQueryBuilder(self::QUERY_SELECT);
        $queryBuilder
            ->cols([
                'app.`id`', 'app.`login`', 'app.`prenom`', 'app.`nom`',
                'app.`email`', 'apm.`id`', 'apm.`raison_sociale`', 'apm.`max_members`', 'app.`id_personne_morale`'
            ])
            ->from('afup_personnes_physiques app')
            ->join('LEFT', 'afup_personnes_morales apm', 'apm.id = app.id_personne_morale')
            ->join('LEFT', 'afup_cotisations ac', 'ac.type_personne = IF(apm.id IS NULL, 0, 1) AND ac.id_personne = IFNULL(apm.id, app.id)')
            ->groupBy(['app.`id`'])
        ;

        return $queryBuilder;
    }

    private function getQueryBuilderWithCompleteUser()
    {
        return $this
            ->getQueryBuilderWithSubscriptions()
            ->cols([
                'app.`id`', 'app.`id_personne_morale`', 'app.`login`', 'app.`mot_de_passe`', 'app.`niveau`',
                'app.`niveau_modules`', 'app.`roles`', 'app.`civilite`', 'app.`nom`', 'app.`prenom`', 'app.`email`',
                'app.`adresse`', 'app.`code_postal`', 'app.`ville`', 'app.`id_pays`', 'app.`telephone_fixe`',
                'app.`telephone_portable`', 'app.`etat`', 'app.`date_relance`', 'app.`compte_svn`',
                'MD5(CONCAT(app.`id`, \'_\', app.`email`, \'_\', app.`login`)) as hash',
                "MAX(ac.date_fin) AS lastsubcription"
            ]);
    }

    /**
     * Add a condition about the type of users: physical, legal or all
     *
     * @param SelectInterface $queryBuilder
     * @param $userType
     */
    private function addUserTypeCondition(SelectInterface $queryBuilder, $userType)
    {
        if ($userType === self::USER_TYPE_PHYSICAL) {
            $queryBuilder->where('id_personne_morale = 0');
        } elseif ($userType === self::USER_TYPE_COMPANY) {
            $queryBuilder->where('id_personne_morale <> 0');
        } elseif ($userType !== self::USER_TYPE_ALL) {
            throw new \UnexpectedValueException(sprintf('Unknown user type "%s"', $userType));
        }
    }

    private function getHydratorForUser()
    {
        return (new HydratorSingleObject())
            ->mapAliasTo('lastsubcription', 'app', 'setLastSubscription')
            ->mapAliasTo('hash', 'app', 'setHash')
            ->mapObjectTo('apm', 'app', 'setCompany')
        ;
    }

    /**
     * Retrieve all users by the date of end of membership.
     *
     * @param int $userType one of self::USER_TYPE_*
     * @return \CCMBenchmark\Ting\Repository\CollectionInterface
     */
    public function getActiveMembers($userType = self::USER_TYPE_PHYSICAL)
    {
        $today = new \DateTimeImmutable();
        $queryBuilder = $this->getQueryBuilderWithSubscriptions();
        $queryBuilder
            ->where('app.`etat` = :status')
            ->having('MAX(ac.`date_fin`) > :start ')
        ;

        $this->addUserTypeCondition($queryBuilder, $userType);

        return $this
            ->getPreparedQuery($queryBuilder->getStatement())
            ->setParams([
                'start' => $today->format('U'),
                'status' => User::STATUS_ACTIVE
            ])
            ->query($this->getCollection(new HydratorSingleObject()));
    }

    /**
     * Retrieve all users by the date of end of membership.
     *
     * @param \DateTimeImmutable $endOfSubscription
     * @param int $userType one of self::USER_TYPE_*
     * @return \CCMBenchmark\Ting\Repository\CollectionInterface
     */
    public function getUsersByEndOfMembership(\DateTimeImmutable $endOfSubscription, $userType = self::USER_TYPE_PHYSICAL)
    {
        $startOfDay = $endOfSubscription->setTime(0, 0, 0);
        $endOfDay = $endOfSubscription->setTime(23, 59, 59);

        $queryBuilder = $this->getQueryBuilderWithSubscriptions();
        $queryBuilder
            ->where('app.`etat` = :status')
            ->having('MAX(ac.`date_fin`) BETWEEN :start AND :end')
        ;

        $this->addUserTypeCondition($queryBuilder, $userType);

        return $this
            ->getPreparedQuery($queryBuilder->getStatement())
            ->setParams([
                'start' => $startOfDay->format('U'),
                'end' => $endOfDay->format('U'),
                'status' => User::STATUS_ACTIVE
            ])
            ->query($this->getCollection(new HydratorSingleObject()));
    }

    public function refreshUser(UserInterface $user)
    {
        if ($this->supportsClass(get_class($user)) === false) {
            throw new UnsupportedUserException(sprintf('Instance of %s not supported', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
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
