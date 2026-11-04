<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'security:reset-backoffice-roles',
    description: 'Wipe les droits back-office portés par la colonne roles',
)]
class ResetBackofficeRolesCommand extends Command
{
    /** Rôles conservés par le wipe. */
    private const array KEPT_ROLES = ['ROLE_COMPANY_MANAGER'];

    /** Préfixe des rôles conservés par le wipe. */
    private const string KEPT_ROLES_PREFIX = 'ROLE_MEMBER_';

    /** Rôles ajoutés dynamiquement par User::getRoles(), jamais persistés. */
    private const array COMPUTED_ROLES = ['ROLE_USER', 'ROLE_MEMBER_EXPIRED'];

    public function __construct(private readonly UserRepository $userRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE)
            ->setHelp(
                'Tous les rôles ne correspondant pas aux rôles conservés sont retirés : ROLE_ADMIN, ROLE_SUPER_ADMIN,'
                . ' les rôles legacy (ROLE_VEILLE, …) et les rôles des modules. Les accès sont ensuite réattribués'
                . " depuis l'admin.\n\n"
                . "Renseigner d'abord son propre compte (ROLE_SUPER_ADMIN via la DB) pour ne pas perdre l'accès à l'admin."
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = (bool) $input->getOption('dry-run');

        $wipe = [];
        foreach ($this->userRepository->loadAll() as $user) {
            $before = $this->storedRoles($user);
            $kept = $this->keptRoles($before);

            if ($kept === $before) {
                continue;
            }

            $wipe[$user->getId()] = [$user, $kept];
        }

        $io->title($dryRun ? 'Wipe des droits back-office (dry-run)' : 'Wipe des droits back-office');

        if (count($wipe) === 0) {
            $io->success('Aucune personne physique concernée, rien à faire.');
            return Command::SUCCESS;
        }

        $io->text('Personnes physiques impactées : ' . count($wipe));
        $this->writeRolesDiff($io, $wipe);

        if (!$dryRun && !$io->confirm('Wiper les rôles de ' . count($wipe) . ' personne(s) physique(s) ?', false)) {
            $io->warning('Wipe annulé, aucun changement fait.');
            return Command::FAILURE;
        }

        foreach ($wipe as [$user, $kept]) {
            $user->setRoles($kept);
            $this->userRepository->save($user);
        }

        $io->success(sprintf('%d personne(s) physique(s) mise(s) à jour.%s', count($wipe), $dryRun ? ' (dry-run)' : ''));

        return Command::SUCCESS;
    }

    /**
     * Détail des rôles modifiés, une ligne par personne physique impactée.
     */
    private function writeRolesDiff(SymfonyStyle $io, array $wipe): void
    {
        $rows = [];
        foreach ($wipe as [$user, $kept]) {
            $before = $this->storedRoles($user);
            $rows[] = [
                $user->getId(),
                $user->getUsername(),
                implode(', ', $before),
                implode(', ', $kept) ?: '-',
            ];
        }

        $io->table(['Id', 'Login', 'Avant', 'Après'], $rows);
    }

    /**
     * Rôles tels qu'ils sont stockés en base. Les défauts ajoutés par User::getRoles()
     * (ROLE_USER et ROLE_MEMBER_EXPIRED, recalés dynamiquement) ne sont jamais persistés.
     */
    private function storedRoles(User $user): array
    {
        return array_values(array_diff($user->getRoles(), self::COMPUTED_ROLES));
    }

    /**
     * Rôles conservés après le wipe : tout ce qui n'est pas admin, legacy ni rôles de modules.
     */
    private function keptRoles(array $roles): array
    {
        return array_values(array_filter(
            $roles,
            fn(string $role): bool => in_array($role, self::KEPT_ROLES, true)
                || str_starts_with($role, self::KEPT_ROLES_PREFIX),
        ));
    }
}
