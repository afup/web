<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Security\LegacyHasher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'security:rehash-legacy-passwords',
    description: 'Wrap MD5 password hashes in Argon2id',
)]
class RehashLegacyPasswordsCommand extends Command
{
    public function __construct(private readonly UserRepository $userRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('dry-run', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = (bool) $input->getOption('dry-run');

        $wrapped = 0;
        $skipped = 0;
        $empty = 0;

        foreach ($this->userRepository->loadAll() as $user) {
            $password = (string) $user->getPassword();

            // Certains users n'ont pas de mot de passe, ou alors pas en md5 ni en argon2.
            // Ils ne peuvent donc pas se connecter, donc on supprime leur mot de passe.
            // Cela les force à faire un reset.
            if (strlen($password) < 32) {
                if (!$dryRun) {
                    $user->setPassword(null);
                    $this->userRepository->save($user);
                }

                $empty++;
                continue;
            }

            // Déjà wrappé ou pas en md5
            if (str_starts_with($password, LegacyHasher::MD5_WRAPPED_PREFIX)
                || strlen($password) !== 32
            ) {
                $skipped++;
                continue;
            }

            // Si on arrive ici, le mot de passe est en md5
            if (!$dryRun) {
                $argon2idHash = password_hash($password, PASSWORD_ARGON2ID);

                $newHash = LegacyHasher::MD5_WRAPPED_PREFIX . $argon2idHash;

                $user->setPassword($newHash);
                $this->userRepository->save($user);
            }

            $wrapped++;
        }

        $io->success(sprintf(
            'Wrapped: %d | Skipped: %d | Empty: %d%s',
            $wrapped,
            $skipped,
            $empty,
            $dryRun ? ' (dry-run)' : '',
        ));

        return Command::SUCCESS;
    }
}
