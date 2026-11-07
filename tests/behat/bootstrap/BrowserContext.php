<?php

declare(strict_types=1);

namespace Afup\Tests\Behat\Bootstrap;

use Behat\Mink\Driver\PantherDriver;
use Behat\Mink\Exception\ExpectationException;
use Behat\Step\Then;
use Behat\Step\When;
use Facebook\WebDriver\Exception\NoAlertOpenException;
use Facebook\WebDriver\Exception\NoSuchAlertException;
use Facebook\WebDriver\Exception\TimeoutException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverExpectedCondition;

/**
 * Étapes propres aux scénarios exécutés dans un vrai navigateur (@javascript).
 */
trait BrowserContext
{
    /**
     * Le localStorage survit d'un scénario à l'autre (Panther ne vide que les cookies) :
     * à appeler sur la page concernée, qui est ensuite rechargée.
     */
    #[When('I clear the local storage')]
    public function clearLocalStorage(): void
    {
        $this->minkContext->getSession()->executeScript('window.localStorage.clear();');
        $this->minkContext->getSession()->reload();
    }

    /**
     * Pour les boutons présents plusieurs fois dans la page, dont un seul est affiché
     * (ex. « Suivant » d'un formulaire en plusieurs étapes). Attend ensuite que la page
     * soit immobile.
     */
    #[When('I press the visible :button button')]
    public function pressVisibleButton(string $button): void
    {
        $page = $this->minkContext->getSession()->getPage();

        foreach ($page->findAll('named', ['button', $button]) as $element) {
            if ($element->isVisible()) {
                $element->press();
                $this->waitUntilThePageIsStill();

                return;
            }
        }

        throw new ExpectationException(
            sprintf('No visible button "%s" found', $button),
            $this->minkContext->getSession()->getDriver(),
        );
    }

    /**
     * Vérifie le texte de la boîte de dialogue `alert()` ouverte, puis la ferme.
     */
    #[Then('I should see the alert :text')]
    public function assertAlert(string $text): void
    {
        $webDriver = $this->webDriver();

        try {
            $webDriver->wait(5)->until(WebDriverExpectedCondition::alertIsPresent());
        } catch (TimeoutException) {
            throw new ExpectationException(
                sprintf('No alert appeared, expected "%s"', $text),
                $this->minkContext->getSession()->getDriver(),
            );
        }

        $alert = $webDriver->switchTo()->alert();
        $actual = $alert->getText();
        $alert->accept();

        if ($actual !== $text) {
            throw new ExpectationException(
                sprintf('The alert says "%s", expected "%s"', $actual, $text),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    /**
     * Un changement d'étape fait défiler la page et apparaître la nouvelle étape en fondu :
     * tant que la page bouge, un clic peut tomber à côté de sa cible.
     */
    private function waitUntilThePageIsStill(): void
    {
        $webDriver = $this->webDriver();
        $previousScroll = null;

        $this->waitForCondition(
            function () use ($webDriver, &$previousScroll): bool {
                // Exécuter un script fermerait l'alerte, que l'étape suivante doit lire.
                if ($this->isAlertOpen($webDriver)) {
                    return true;
                }

                [$scroll, $runningAnimations] = $webDriver->executeScript(
                    'return [window.scrollY, document.getAnimations().length];',
                );
                $isStill = 0 === $runningAnimations && $scroll === $previousScroll;
                $previousScroll = $scroll;

                return $isStill;
            },
            'The page is still moving',
        );
    }

    private function isAlertOpen(RemoteWebDriver $webDriver): bool
    {
        try {
            $webDriver->switchTo()->alert()->getText();

            return true;
        } catch (NoAlertOpenException|NoSuchAlertException) {
            return false;
        }
    }

    private function webDriver(): RemoteWebDriver
    {
        $driver = $this->minkContext->getSession()->getDriver();

        if (!$driver instanceof PantherDriver) {
            throw new \LogicException('This step requires a @javascript scenario.');
        }

        $webDriver = $driver->getClient()->getWebDriver();
        \assert($webDriver instanceof RemoteWebDriver);

        return $webDriver;
    }
}
