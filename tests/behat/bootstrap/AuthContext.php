<?php

declare(strict_types=1);

namespace Afup\Tests\Behat\Bootstrap;

use Behat\Mink\Exception\ExpectationException;
use Behat\Step\Given;
use Behat\Step\When;
use Facebook\WebDriver\Exception\StaleElementReferenceException;

trait AuthContext
{
    private const RETRY_DELAY_MS = 500;
    private const MAX_RETRIES = 3;

    #[Given('I am logged in as admin and on the Administration')]
    public function iAmLoggedInAsAdminAndOnTheAdministration(): void
    {
        $this->iAmLoggedInAsAdmin();
        try {
            $this->minkContext->clickLink('Administration');
        } catch (StaleElementReferenceException $exception) {
            // Le DOM est remplacé pendant la redirection post-login : on retente.
            $this->retry(fn() => $this->minkContext->clickLink('Administration'), $exception);
        }
    }

    private function retry(callable $callback, ?\Throwable $previous = null): void
    {
        $exception = $previous;

        for ($attempt = 0; $attempt < self::MAX_RETRIES; $attempt++) {
            usleep(self::RETRY_DELAY_MS * 1000);

            try {
                $callback();

                return;
            } catch (\Throwable $thrown) {
                $exception = $thrown;
            }
        }

        throw new ExpectationException(
            sprintf('Step failed after %d attempts: %s', self::MAX_RETRIES, $exception?->getMessage()),
            $this->minkContext->getSession()->getDriver(),
            $exception,
        );
    }

    #[Given('I am logged in as admin')]
    public function iAmLoggedInAsAdmin(): void
    {
        $this->iAmLoggedInWithTheUserAndThePassword('admin', 'admin');
    }

    #[Given('I am logged-in with the user :username and the password :password')]
    public function iAmLoggedInWithTheUserAndThePassword(string $username, string $password): void
    {
        $this->minkContext->visitPath('/login');
        $this->minkContext->fillField('utilisateur', $username);
        $this->minkContext->fillField('mot_de_passe', $password);
        $this->minkContext->pressButton('Se connecter');
        $this->minkContext->assertPageContainsText('Espace membre');
    }

    #[When('I request a password reset for :arg1')]
    public function iRequestAPasswordReset(string $arg1): void
    {
        $this->minkContext->iAmOnHomepage();
        $this->minkContext->assertPageContainsText("Tous les trois mois, des nouvelles de L'AFUP");
        $this->minkContext->clickLink("Se connecter");
        $this->minkContext->assertPageContainsText("Email ou nom d'utilisateur");
        $this->minkContext->clickLink("Mot de passe perdu");
        $this->minkContext->assertPageContainsText("Mot de passe perdu");
        $this->minkContext->fillField("form_email", $arg1);
        $this->minkContext->pressButton("Demander un nouveau mot de passe");
        $this->minkContext->assertPageContainsText("Votre demande a été prise en compte. Si un compte correspond à cet email vous recevez un nouveau mot de passe rapidement.");
    }
}
