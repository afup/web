<?php

declare(strict_types=1);

namespace AppBundle\Association\PasswordReset;

use AppBundle\Association\Entity\PasswordResetToken;
use AppBundle\Association\Entity\Repository\PasswordResetTokenRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use Psr\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

/**
 * Réinitialisation de mot de passe par lien avec token.
 *
 * Le token en clair n'existe que dans l'e-mail : seul son SHA-256 est stocké.
 * La clé primaire de la table étant l'identifiant du membre, un seul token peut
 * exister à un instant T pour un compte donné — une nouvelle demande remplace la
 * précédente, et la consommation supprime la ligne.
 */
class PasswordResetService
{
    public function __construct(
        private readonly PasswordResetTokenRepository $passwordResetTokenRepository,
        private readonly UserRepository $userRepository,
        private readonly PasswordResetMailer $passwordResetMailer,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ClockInterface $clock,
        #[Autowire('%app.password_reset_token_ttl%')]
        private readonly int $ttlInSeconds,
    ) {}

    /**
     * Ne signale pas les emails inconnus, pour ne pas révéler quels emails
     * correspondent à un compte.
     */
    public function requestResetForEmail(string $email): void
    {
        try {
            $user = $this->userRepository->loadUserByEmailOrAlternateEmail($email);
        } catch (UserNotFoundException) {
            return;
        }

        $this->createTokenFor($user);
    }

    /**
     * @return bool true si l'e-mail a pu être envoyé
     */
    public function createTokenFor(User $user): bool
    {
        $plainToken = bin2hex(random_bytes(32));
        $now = $this->clock->now();

        $token = $this->passwordResetTokenRepository->find($user->getId()) ?? new PasswordResetToken();
        $token->userId = $user->getId();
        $token->tokenHash = $this->hash($plainToken);
        $token->createdAt = $now;
        $token->expiresAt = $now->add(new \DateInterval(sprintf('PT%dS', $this->ttlInSeconds)));

        // Le token doit être enregistré avant l'envoi : un e-mail parti alors que
        // l'écriture a échoué contiendrait un lien mort.
        $this->passwordResetTokenRepository->save($token);

        // Purge faite après l'enregistrement : un DELETE DQL ne met pas à jour les
        // entités déjà chargées, l'inverse risquerait de mettre à jour une ligne
        // supprimée. Le token qui vient d'être créé n'est pas expiré, il reste.
        $this->passwordResetTokenRepository->deleteExpired($now);

        return $this->passwordResetMailer->sendResetLink($user, $plainToken, $this->getValidityInMinutes());
    }

    public function findValidToken(string $plainToken): ?PasswordResetToken
    {
        return $this->passwordResetTokenRepository->findValidToken($this->hash($plainToken), $this->clock->now());
    }

    /**
     * @return bool false si l'utilisateur visé par le token n'existe plus
     */
    public function reset(PasswordResetToken $token, string $plainPassword): bool
    {
        $user = $this->userRepository->get($token->userId);
        if (!$user instanceof User) {
            return false;
        }

        // Ting et Doctrine écrivent sur deux connexions distinctes, sans transaction
        // commune : on consomme le token d'abord. Si l'écriture du mot de passe
        // échoue ensuite, le membre redemande un lien — l'ordre inverse laisserait
        // un token rejouable après un changement réussi.
        $this->passwordResetTokenRepository->delete($token);

        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        $this->userRepository->save($user);

        return true;
    }

    public function getValidityInMinutes(): int
    {
        return intdiv($this->ttlInSeconds, 60);
    }

    private function hash(string $plainToken): string
    {
        return hash('sha256', $plainToken);
    }
}
