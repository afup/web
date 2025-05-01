<?php

declare(strict_types=1);

namespace AppBundle\Association\Model\Repository;

use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\User;
use AppBundle\Event\Model\Badge;
use Assert\Assertion;
use Aura\SqlQuery\Common\SelectInterface;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use UnexpectedValueException;

/**
 * @extends Repository<User>
 */
class UserRepository extends Repository implements MetadataInitializer, UserProviderInterface, PasswordUpgraderInterface
{
    const USER_TYPE_PHYSICAL = 0;
    const USER_TYPE_COMPANY = 1;
    const USER_TYPE_ALL = 2;

    public function loadUserByIdentifier(string $identifier): User
    {
        return $this->loadUserByUsername($identifier);
    }

    public function loadUserByUsername(string $username): User
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
                'email' => $username,
            ])
            ->query($this->getCollection($this->getHydratorForUser()));

        if (!$result || $result->count() === 0) {
            throw new UserNotFoundException(sprintf('Could not find the user with login "%s"', $username));
        }

        return $result->first();
    }

    /**
     * @param User $user
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        $user->setPassword($newHashedPassword);
        $this->save($user);
    }

    public function loadUserByEmailOrAlternateEmail($email)
    {
        $queryBuilder = $this->getQueryBuilderWithCompleteUser();
        $queryBuilder
            ->where('app.`email` = :email')
            ->orWhere('app.`slack_alternate_email` = :slack_alternate_email')
        ;
        $result = $this
            ->getPreparedQuery($queryBuilder->getStatement())
            ->setParams([
                'email' => $email,
                'slack_alternate_email' => $email,
            ])
            ->query($this->getCollection($this->getHydratorForUser()));

        if ($result->count() === 0) {
            throw new UserNotFoundException(sprintf('Could not find the user with email "%s"', $email));
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
                'hash' => $hash,
            ])
            ->query($this->getCollection($this->getHydratorForUser()));

        if ($result->count() === 0) {
            throw new UserNotFoundException(sprintf('Could not find the user with hash "%s"', $hash));
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
                'state' => User::STATUS_ACTIVE,
            ])
            ->query($this->getCollection($this->getHydratorForUser()))
            ;
    }

    /**
     * @return CollectionInterface|User[]
     *
     * @throws \CCMBenchmark\Ting\Exception
     */
    public function loadAll()
    {
        $queryBuilder = $this->getQueryBuilderWithCompleteUser();


        return $this
            ->getQuery($queryBuilder->getStatement())
            ->query($this->getCollection($this->getHydratorForUser()))
        ;
    }

    public function loadByBadge(Badge $badge)
    {
        $queryBuilder = $this->getQueryBuilderWithCompleteUser();

        $queryBuilder->join('INNER', 'afup_personnes_physiques_badge', 'app.id = afup_personnes_physiques_badge.afup_personne_physique_id');
        $queryBuilder->where('afup_personnes_physiques_badge.badge_id = :badge_id');

        return $this
            ->getPreparedQuery($queryBuilder->getStatement())
            ->setParams(['badge_id' => $badge->getId()])
            ->query($this->getCollection($this->getHydratorForUser()))
        ;
    }

    /**
     * Renvoie la liste des personnes physiques
     *
     * @param bool      $onlyActive
     * @param string    $sort Tri des enregistrements
     * @param int|int[] $userId
     *
     * @return CollectionInterface&iterable<User>
     */
    public function search(
        $sort = 'lastname',
        $direction = 'asc',
        $filter = null,
        $companyId = null,
        $userId = null,
        $onlyActive = true,
        $isCompanyManager = null,
        $needsUptoDateMembership = null,
    ) {
        Assertion::inArray($direction, ['asc', 'desc']);
        $sorts = [
            'lastname' => ['nom', 'prenom'],
            'firstname' => ['prenom', 'nom'],
            'status' => ['etat','nom', 'prenom'],
        ];
        Assertion::keyExists($sorts, $sort);

        $queryBuilder = $this->getQueryBuilderWithCompleteUser()
            ->orderBy(array_map(static fn ($field): string => $field . ' ' . $direction, $sorts[$sort]));

        // On filtre sur tous les mots possibles. Donc plus on a de mots dans la recherche plus on aura de résultats.
        // Mais ça peut aussi permettre de trouver des personnes en entrant par exemple "Prénom email" dans le champ de recherche :
        //   Même si l'email ne colle pas on pourra trouver la personne.
        // C'est un peu barbare mais généralement on ne met qu'un seul terme dans la recherche… du coup c'est pas bien grave.
        if ($filter) {
            $filters = explode(' ', (string) $filter);
            $filters = array_filter(array_map('trim', $filters));
            $ors = [];
            foreach ($filters as $i => $value) {
                $ors[] = "app.login LIKE :filter$i OR app.nom LIKE :filter$i OR app.prenom LIKE :filter$i
                    OR app.code_postal LIKE :filter$i OR app.ville LIKE :filter$i OR app.email LIKE :filter$i";
                $queryBuilder->bindValue('filter' . $i, '%' . $value . '%');
            }
            $queryBuilder->where('(' . implode(' OR ', $ors) . ')');
        }
        if ($companyId) {
            $queryBuilder->where('app.id_personne_morale = :companyId')
                ->bindValue('companyId', $companyId);
        }
        if ($userId) {
            if (!is_array($userId)) {
                $userId = [$userId];
            }
            $queryBuilder->where('app.id IN (:userIds)')
                ->bindValue('userIds', $userId);
        }
        if ($onlyActive) {
            $queryBuilder->where('app.etat = :status')
                ->bindValue('status', User::STATUS_ACTIVE);
        }
        if ($isCompanyManager) {
            $queryBuilder->where('app.roles LIKE \'%ROLE_COMPANY_MANAGER%\'');
        }
        if ($needsUptoDateMembership) {
            $queryBuilder->where('app.needs_up_to_date_membership = 1');
        }

        return $this
            ->getQuery($queryBuilder->getStatement())
            ->setParams($queryBuilder->getBindValues())
            ->query($this->getCollection($this->getHydratorForUser()));
    }

    /**
     * Ajoute une personne physique
     */
    public function create(User $user): void
    {
        if ($this->loginExists($user->getUsername())) {
            throw new InvalidArgumentException('Il existe déjà un compte pour ce login.');
        }
        if ($this->emailExists($user->getEmail())) {
            throw new InvalidArgumentException('Il existe un compte avec cette adresse email.');
        }
        if (0 !== $user->getCompanyId() && !$this->companyExists($user->getCompanyId())) {
            throw new InvalidArgumentException('La personne morale n\'existe pas.');
        }
        if (null !== $user->getCountry() && !$this->countryExists($user->getCountry())) {
            throw new InvalidArgumentException('Le pays n\'existe pas.');
        }
        try {
            $this->save($user);
        } catch (Exception $e) {
            throw new RuntimeException("Impossible d'enregistrer l'utilisateur à cause d'une erreur SQL. Veuillez contacter le bureau !", $e->getCode(), $e);
        }
    }

    /**
     * @param string $login Person's login
     * @param int    $id    Identifier to ignore
     *
     * @return bool Login in use (TRUE) or not (FALSE)
     */
    public function loginExists($login, $id = 0): bool
    {
        return 0 < $this->getQuery('SELECT 1 FROM afup_personnes_physiques WHERE login = :login AND id <> :id')
                ->setParams(['login' => $login, 'id' => $id])
                ->query()->count();
    }

    public function edit(User $user): void
    {
        if ($this->loginExists($user->getUsername(), $user->getId())) {
            throw new InvalidArgumentException('Il existe déjà un compte pour ce login.');
        }
        if (0 !== $user->getCompanyId() && !$this->companyExists($user->getCompanyId())) {
            throw new InvalidArgumentException('La personne morale n\'existe pas.');
        }
        if (null !== $user->getCountry() && !$this->countryExists($user->getCountry())) {
            throw new InvalidArgumentException('Le pays n\'existe pas.');
        }
        $this->save($user);
    }

    public function remove(User $user): void
    {
        $nbCotisations = (int) $this->getQuery('SELECT COUNT(*) nb FROM afup_cotisations WHERE type_personne = :memberType AND id_personne = :id')
            ->setParams(['memberType' => AFUP_PERSONNES_PHYSIQUES, 'id' => $user->getId()])
            ->query()->first()[0]->nb;
        if (0 < $nbCotisations) {
            throw new InvalidArgumentException('Impossible de supprimer une personne physique qui a des cotisations');
        }

        $this->delete($user);
    }

    /**
     * @return CollectionInterface&iterable<User>
     */
    public function getAdministrators()
    {
        $queryBuilder = $this->getQueryBuilderWithCompleteUser()
            ->where('niveau_modules <> 0 OR niveau = :level')
            ->orderBy(['nom', 'prenom']);
        $queryBuilder->bindValue('level', User::LEVEL_ADMIN);

        return $this
            ->getQuery($queryBuilder->getStatement())
            ->setParams($queryBuilder->getBindValues())
            ->query($this->getCollection($this->getHydratorForUser()));
    }

    /**
     * @param string $email Person's email
     * @param int    $id    Identifier to ignore
     *
     * @return bool TRUE if the email exists, FALSE otherwise
     */
    private function emailExists($email, $id = 0): bool
    {
        return 0 < $this->getQuery('SELECT 1 FROM afup_personnes_physiques WHERE email = :email AND id <> :id')
                ->setParams(['email' => $email, 'id' => $id])
                ->query()->count();
    }

    /**
     * @param int $companyId Company's identifier
     *
     * @return bool TRUE if the company exists, FALSE otherwise
     */
    private function companyExists($companyId): bool
    {
        return 0 < $this->getQuery('SELECT 1 FROM afup_personnes_morales WHERE id = :id')
                ->setParams(['id' => $companyId])
                ->query()->count();
    }

    /**
     * @param int $countryId Country's identifier
     *
     * @return bool TRUE if the country exists, FALSE otherwise
     */
    private function countryExists($countryId): bool
    {
        return 0 < $this->getQuery('SELECT 1 FROM afup_pays WHERE id = :id')
                ->setParams(['id' => $countryId])
                ->query()->count();
    }

    /**
     * @return SelectInterface
     */
    private function getQueryBuilderWithSubscriptions()
    {
        /**
         * @var SelectInterface $queryBuilder
         */
        $queryBuilder = $this->getQueryBuilder(self::QUERY_SELECT);
        $queryBuilder
            ->cols([
                'app.`id`', 'app.`login`', 'app.`prenom`', 'app.`nom`',
                'app.`email`', 'apm.`id`', 'apm.`raison_sociale`', 'apm.`max_members`', 'app.`id_personne_morale`',
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
                'app.`slack_invite_status`', 'app.`slack_alternate_email`', 'app.`needs_up_to_date_membership`',
                'app.`nearest_office`',
                'MD5(CONCAT(app.`id`, \'_\', app.`email`, \'_\', app.`login`)) as hash',
                "MAX(ac.date_fin) AS lastsubcription",
            ]);
    }

    /**
     * Add a condition about the type of users: physical, legal or all
     *
     * @param $userType
     */
    private function addUserTypeCondition(SelectInterface $queryBuilder, $userType): void
    {
        if ($userType === self::USER_TYPE_PHYSICAL) {
            $queryBuilder->where('id_personne_morale = 0');
        } elseif ($userType === self::USER_TYPE_COMPANY) {
            $queryBuilder->where('id_personne_morale <> 0');
        } elseif ($userType !== self::USER_TYPE_ALL) {
            throw new UnexpectedValueException(sprintf('Unknown user type "%s"', $userType));
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
     * @return CollectionInterface
     */
    public function getActiveMembers($userType = self::USER_TYPE_PHYSICAL)
    {
        $today = new \DateTimeImmutable();
        $queryBuilder = $this->getQueryBuilderWithSubscriptions();
        $queryBuilder
            ->where('app.`etat` = :status')
        ;

        $this->addUserTypeCondition($queryBuilder, $userType);

        return $this
            ->getPreparedQuery($queryBuilder->getStatement())
            ->setParams([
                'start' => $today->format('U'),
                'status' => User::STATUS_ACTIVE,
            ])
            ->query($this->getCollection(new HydratorSingleObject()));
    }

    /**
     * Retrieve all users by the date of end of membership.
     *
     * @param int $userType one of self::USER_TYPE_*
     * @return CollectionInterface
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
                'status' => User::STATUS_ACTIVE,
            ])
            ->query($this->getCollection(new HydratorSingleObject()));
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if ($this->supportsClass($user::class) === false) {
            throw new UnsupportedUserException(sprintf('Instance of %s not supported', $user::class));
        }

        return $this->loadUserByUsername($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
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
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'id_personne_morale',
                'fieldName' => 'companyId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'login',
                'fieldName' => 'username',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'mot_de_passe',
                'fieldName' => 'password',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'civilite',
                'fieldName' => 'civility',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'prenom',
                'fieldName' => 'firstName',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'nom',
                'fieldName' => 'lastName',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'email',
                'fieldName' => 'email',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'niveau',
                'fieldName' => 'level',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'niveau_modules',
                'fieldName' => 'levelModules',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'roles',
                'fieldName' => 'roles',
                'type' => 'json',
                'serializer_options' => [
                    'unserialize' => ['assoc' => true],
                ],
            ])
            ->addField([
                'columnName' => 'adresse',
                'fieldName' => 'address',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'code_postal',
                'fieldName' => 'zipCode',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'ville',
                'fieldName' => 'city',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'id_pays',
                'fieldName' => 'country',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'telephone_fixe',
                'fieldName' => 'phone',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'telephone_portable',
                'fieldName' => 'mobilephone',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'nearest_office',
                'fieldName' => 'nearestOffice',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'etat',
                'fieldName' => 'status',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'slack_invite_status',
                'fieldName' => 'slackInviteStatus',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'slack_alternate_email',
                'fieldName' => 'alternateEmail',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'needs_up_to_date_membership',
                'fieldName' => 'needsUpToDateMembership',
                'type' => 'bool',
                'serializer' => Boolean::class,
            ])
        ;

        return $metadata;
    }
}
