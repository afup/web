<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Symfony\Component\Process\Process;

class FeatureContext implements Context
{

    /** @var \Behat\MinkExtension\Context\MinkContext */
    private $minkContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->minkContext = $environment->getContext('Behat\MinkExtension\Context\MinkContext');
    }

    /**
     * @BeforeScenario @reloadDbWithTestData
     */
    public function beforeScenarioReloadDb()
    {
        $this->resetDb();
        $this->migrateDb();
        $this->seedRun();
    }

    private function resetDb()
    {
        $pdo = new \PDO("mysql:host=dbtest", "root", "root");
        $pdo->exec('DROP DATABASE IF EXISTS web');
        $pdo->exec('CREATE DATABASE web');
    }

    private function migrateDb()
    {
        $this->runCommand( ["./bin/phinx", "migrate", "-e", "test"]);
    }

    private function seedRun()
    {
        $this->runCommand( ["./bin/phinx", "seed:run", "-e", "test"]);
    }

    private function runCommand(array $command)
    {
        $process = new Process($command);
        $process->mustRun();
    }

    /**
     * @Given I am logged in as admin and on the Administration
     */
    public function iAmLoggedInAsAdminAndOnTheAdministration()
    {
        $this->iAmLoggedInAsAdmin();
        $this->minkContext->clickLink("Administration");
    }

    /**
     * @Given I am logged in as admin
     */
    public function iAmLoggedInAsAdmin()
    {

        $this->minkContext->iAmOnHomepage();
        $this->minkContext->assertPageContainsText("Tous les deux mois, des nouvelles de L'AFUP");
        $this->minkContext->clickLink("Se connecter");
        $this->minkContext->assertPageContainsText("Email ou nom d'utilisateur");
        $this->minkContext->fillField("utilisateur", "admin");
        $this->minkContext->fillField("mot_de_passe", "admin");
        $this->minkContext->pressButton("Se connecter");
        $this->minkContext->assertPageContainsText("Espace membre");
    }

    /**
     * @Then The :field field should only contain the follow values :expectedValuesJson
     */
    public function selectHasValues($field, $expectedValuesJson)
    {
        $node = $this->minkContext->assertSession()->fieldExists($field);
        $options = $node->findAll('css', 'option');

        $expectedValues = json_decode($expectedValuesJson, true);

        $foundValues = [];
        foreach ($options as $option) {
            $foundValues[] = $option->getText();
        }

        if ($foundValues != $expectedValues) {
            throw new \Exception(sprintf('The select has the following values %s (expected %s)', json_encode($foundValues, JSON_UNESCAPED_UNICODE), $expectedValuesJson));
        }
    }
}
