<?php

declare(strict_types=1);

namespace Afup\Tests\Behat\Bootstrap;

use Afup\Tests\Support\DatabaseManager;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Hook\AfterScenario;
use Behat\Hook\BeforeScenario;
use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Driver\PantherDriver;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ExpectationException;
use Behat\MinkExtension\Context\MinkContext;
use Symfony\Component\Filesystem\Filesystem;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;

class FeatureContext implements Context
{
    use ApiContext;
    use AuthContext;
    use BrowserContext;
    use EmailContext;
    use FormContext;
    use PdfContext;
    use SeoContext;
    use TimeContext;
    use WaitContext;

    private MinkContext $minkContext;

    private readonly DatabaseManager $databaseManager;

    public function __construct()
    {
        $this->databaseManager = new DatabaseManager(true);

        $this->initTimeContext();
    }

    #[BeforeScenario]
    public function fetchMinkContext(BeforeScenarioScope $scope): void
    {
        $this->minkContext = $scope->getEnvironment()->getContext(MinkContext::class);
    }

    #[BeforeScenario('@reloadDbWithTestData')]
    public function beforeScenarioReloadDatabase(): void
    {
        $this->databaseManager->reloadDatabase();
    }

    #[When('I do not follow redirects')]
    public function doNotFollowRedirects(): void
    {
        $driver = $this->minkContext->getSession()->getDriver();

        if (!$driver instanceof BrowserKitDriver) {
            throw new \LogicException('This step requires a non-@javascript scenario.');
        }

        $driver->getClient()->followRedirects(false);
    }

    #[AfterScenario]
    public function restoreFollowRedirects(): void
    {
        $driver = $this->minkContext->getSession()->getDriver();

        if ($driver instanceof BrowserKitDriver) {
            $driver->getClient()->followRedirects(true);
        }
    }

    /**
     * Les photos des conférenciers ne sont pas versionnées : ce step en dépose une, par exemple « 3/originals/4.png ».
     */
    #[Given('the speaker photo :file is stored as :path')]
    public function storeSpeakerPhoto(string $file, string $path): void
    {
        new Filesystem()->copy(__DIR__ . '/../files/' . $file, __DIR__ . '/../../../htdocs/uploads/speakers/' . $path, true);
    }

    #[Then('simulate the Paybox callback')]
    public function simulateThePayboxCallback(): void
    {
        $url = $this->minkContext->getSession()->getCurrentUrl();
        $url = str_replace('paybox-redirect', 'paybox-callback', $url);

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_exec($curl);
    }

    #[Then('the response header :arg1 should equal :arg2')]
    public function assertResponseHeaderEquals(string $headerName, string $expectedValue): void
    {
        $this->minkContext->assertSession()->responseHeaderEquals($headerName, $expectedValue);
    }

    #[Then('the response header :arg1 should match :arg2')]
    public function assertResponseHeaderMatch(string $headerName, string $regExpExpectedValue): void
    {
        $this->minkContext->assertSession()->responseHeaderMatches($headerName, $regExpExpectedValue);
    }

    #[Then('/^the response should contain the html "(?P<text>(?:[^"]|\\")*)"$/')]
    #[Then('/^the response should contain the html$/')]
    public function assertResponseHasHtml(PyStringNode|string $html): void
    {
        if ($html instanceof PyStringNode) {
            $html = $html->getRaw();
        }

        $this->minkContext->assertResponseContains($html);
    }

    #[Then('the current URL should match :arg1')]
    public function assertCurrentUrlContains(string $regex): void
    {
        $currentUrl = $this->minkContext->getSession()->getCurrentUrl();

        if (!preg_match($regex, $currentUrl)) {
            throw new ExpectationException(
                sprintf('The current URL "%s" does not matches "%s"', $currentUrl, $regex),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    #[When('I follow the button of tooltip :arg1')]
    public function clickLinkOfTooltip(string $tooltip): void
    {
        $link = $this->findTooltipLinkWhenRendered($tooltip, 'a');

        if (null === $link) {
            throw new ExpectationException(
                sprintf('Link of tooltip "%s" not found', $tooltip),
                $this->minkContext->getSession()->getDriver(),
            );
        }

        $link->click();
    }

    #[When('I press the button of tooltip :arg1')]
    public function pressButtonOfTooltip(string $tooltip): void
    {
        $button = $this->findTooltipLinkWhenRendered($tooltip, 'button');

        if (null === $button) {
            throw new ExpectationException(
                sprintf('Button of tooltip "%s" not found', $tooltip),
                $this->minkContext->getSession()->getDriver(),
            );
        }

        $button->press();
    }

    #[When('/^I press "(?P<button>(?:[^"]|\\")*)" in the row of "(?P<row>(?:[^"]|\\")*)"$/')]
    public function pressButtonInRow(string $button, string $row): void
    {
        $input = $this->minkContext->getSession()->getPage()->find(
            'xpath',
            sprintf('//tr[contains(., "%s")]//input[@value="%s"]', $row, $button),
        );

        if (null === $input) {
            throw new ExpectationException(
                sprintf('Button "%s" in row "%s" not found', $button, $row),
                $this->minkContext->getSession()->getDriver(),
            );
        }

        $input->press();
    }

    private function findTooltipLinkWhenRendered(string $tooltip, string $tag): ?NodeElement
    {
        $session = $this->minkContext->getSession();
        $find = fn(): ?NodeElement => $session->getPage()->find('css', sprintf('%s[data-tooltip="%s"]', $tag, $tooltip));
        $element = $find();

        if (null === $element && $session->getDriver() instanceof PantherDriver) {
            $this->waitForCondition(
                fn(): bool => $find() !== null,
                sprintf('Tooltip "%s" not rendered', $tooltip),
            );
            $element = $find();
        }

        return $element;
    }

    #[Then('/^the rows of table "(?P<selector>[^"]+)" should be in the following order:$/')]
    public function assertTableRowsInOrder(string $selector, PyStringNode $expectedRows): void
    {
        $expected = array_values(array_filter(array_map('trim', $expectedRows->getStrings())));
        $rows = $this->minkContext->getSession()->getPage()->findAll('css', $selector . ' tbody tr');

        if (count($expected) !== count($rows)) {
            throw new ExpectationException(
                sprintf('Expected %d rows but found %d in "%s"', count($expected), count($rows), $selector),
                $this->minkContext->getSession()->getDriver(),
            );
        }

        foreach ($expected as $index => $expectedText) {
            $actualText = $rows[$index]->getText();
            if (!str_contains($actualText, $expectedText)) {
                throw new ExpectationException(
                    sprintf('Row %d of "%s" should contain "%s" but contains "%s"', $index + 1, $selector, $expectedText, $actualText),
                    $this->minkContext->getSession()->getDriver(),
                );
            }
        }
    }

    #[Then('the checksum of the response content should be :md5')]
    public function checksumOfTheResponseContentShouldBe(string $expectedChecksum): void
    {
        $content = $this->minkContext->getSession()->getPage()->getContent();

        $foundChecksum = md5($content);

        if ($expectedChecksum !== $foundChecksum) {
            throw new ExpectationException(
                sprintf('The checksum %s is not the expected checksum %s', $foundChecksum, $expectedChecksum),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    #[Then('/^(?:|I )should see tooltip "(?P<value>(?:[^"]|\\")*)"$/')]
    public function shouldSeeTooltip(string $tooltip): void
    {
        $link = $this->minkContext->getSession()->getPage()->find('css', sprintf('a[data-tooltip="%s"]', $tooltip));

        if (null === $link) {
            throw new ExpectationException(
                sprintf('Tooltip "%s" was not found', $tooltip),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    #[Then('/^(?:|I )should not see tooltip "(?P<value>(?:[^"]|\\")*)"$/')]
    public function shouldNotSeeTooltip(string $tooltip): void
    {
        $link = $this->minkContext->getSession()->getPage()->find('css', sprintf('a[data-tooltip="%s"]', $tooltip));

        if (null !== $link) {
            throw new ExpectationException(
                sprintf('Tooltip "%s" was found', $tooltip),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    #[Then('/^the downloaded file should (?P<mode>strictly )?be the same as "(?P<filename>(?:[^"]|\\")*)"$/')]
    public function assertDownloadedFile(string $mode, string $filename): void
    {
        if ($this->minkContext->getMinkParameter('files_path')) {
            $fullPath = rtrim(realpath($this->minkContext->getMinkParameter('files_path')), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
            if (is_file($fullPath)) {
                $filename = $fullPath;
            }
        }
        $expected = file_get_contents($filename);
        if ('strictly' === trim($mode)) {
            $value = $this->minkContext->getSession()->getDriver()->getContent();
        } else {
            $value = $this->minkContext->getSession()->getPage()->getContent(); // uses trim()
        }

        if ($expected !== $value) {
            throw new ExpectationException(
                sprintf('The downloaded file is not same as "%s"', $filename),
                $this->minkContext->getSession()->getDriver(),
            );
        }

    }

    /**
     * @throws ExpectationException
     * exemples:
     * I should see a red label "oui"
     * I should see a label "ok"
     */
    #[Then('/^(?:|I )should see a (?P<color>(?:[\w])* )?label "(?P<value>(?:[^"]|\\")*)"$/')]
    public function shouldSeeLabel(string $color, string $text): void
    {
        $labels = $this->minkContext->getSession()->getPage()->findAll('css', sprintf('.ui.label%s', $color != '' ? ('.' . $color) : ''));

        foreach ($labels as $label) {
            if (trim((string) $label->getText()) === $text) {
                return;
            }
        }

        throw new ExpectationException(
            sprintf('label "%s" was not found', $text),
            $this->minkContext->getSession()->getDriver(),
        );
    }

    /**
     * @throws ExpectationException
     * exemples:
     * I should see an image with source "image.png"
     */
    #[Then('/^(?:|I )should see an image with source "(?P<source>(?:[^"]|\\")*)"$/')]
    public function shouldSeeImage(string $source): void
    {
        $image = $this->minkContext->getSession()->getPage()->find('css', sprintf('img[src="%s"]', $source));

        if (null === $image) {
            throw new ExpectationException(
                sprintf('image with source "%s" was not found', $source),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    /**
     * Échoue aussi si aucun lien ne correspond, pour ne pas passer à vide.
     * exemple:
     * all links with href containing "/joindin" should have rel containing "nofollow"
     */
    #[Then('/^all links with href containing "(?P<href>(?:[^"]|\\")*)" should have rel containing "(?P<rel>(?:[^"]|\\")*)"$/')]
    public function allLinksShouldHaveRel(string $href, string $rel): void
    {
        $links = $this->minkContext->getSession()->getPage()->findAll('css', sprintf('a[href*="%s"]', $href));

        if ([] === $links) {
            throw new ExpectationException(
                sprintf('No link with href containing "%s" was found', $href),
                $this->minkContext->getSession()->getDriver(),
            );
        }

        foreach ($links as $link) {
            $relTokens = preg_split('/\s+/', (string) $link->getAttribute('rel'), -1, PREG_SPLIT_NO_EMPTY);

            if (!in_array($rel, $relTokens, true)) {
                throw new ExpectationException(
                    sprintf('The link "%s" has rel "%s", which does not contain "%s"', $link->getAttribute('href'), $link->getAttribute('rel'), $rel),
                    $this->minkContext->getSession()->getDriver(),
                );
            }
        }
    }

    #[Then('the page should have a meta description')]
    public function assertMetaDescriptionIsNotEmpty(): void
    {
        if ($this->getMetaDescription() === '') {
            throw new ExpectationException('The meta description is empty', $this->minkContext->getSession()->getDriver());
        }
    }

    #[Then('the meta description should be :expected')]
    public function assertMetaDescriptionEquals(string $expected): void
    {
        $actual = $this->getMetaDescription();

        if ($actual !== $expected) {
            throw new ExpectationException(
                sprintf('The meta description is "%s", "%s" expected', $actual, $expected),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    private function getMetaDescription(): string
    {
        $metas = $this->minkContext->getSession()->getPage()->findAll('css', 'meta[name="description"]');

        if (count($metas) !== 1) {
            throw new ExpectationException(
                sprintf('Exactly one meta description expected, %d found', count($metas)),
                $this->minkContext->getSession()->getDriver(),
            );
        }

        return trim((string) $metas[0]->getAttribute('content'));
    }

    #[Then('/^(?:|I )click on link with (class|id) "(?P<text>(?:[^"]|\\")*)"$/')]
    public function clickOnLink(string $type, string $text): void
    {
        $selector = match ($type) {
            'class' => 'a.' . $text,
            'id' => 'a#' . $text,
        };
        $node = $this->minkContext->getSession()->getPage()->find('css', $selector);

        if (null === $node) {
            throw new ExpectationException(
                sprintf('link with %s "%s" was not found', $type, $selector),
                $this->minkContext->getSession()->getDriver(),
            );
        }

        $node->click();
    }

    #[Then('/^(?:|I )open menu "(?P<text>(?:[^"]|\\")*)"$/')]
    public function openMenu(string $text): void
    {
        $node = $this->minkContext->getSession()->getPage()->find('css', 'div.header.title:contains("' . $text . '")');

        if (null === $node) {
            throw new ExpectationException(
                sprintf('menu "%s" was not found', $text),
                $this->minkContext->getSession()->getDriver(),
            );
        }

        $node->click();
    }

    #[Then('the JSON-LD :type should have the following properties:')]
    public function assertJsonLdHasProperties(string $type, TableNode $expectedProperties): void
    {
        $data = $this->findJsonLd($type);

        if (null === $data) {
            throw new ExpectationException(sprintf('No JSON-LD of type "%s" found', $type), $this->minkContext->getSession()->getDriver());
        }

        foreach ($expectedProperties->getRowsHash() as $property => $expectedValue) {
            $actualValue = $data[$property] ?? null;
            if (!is_scalar($actualValue) || (string) $actualValue !== $expectedValue) {
                throw new ExpectationException(
                    sprintf('JSON-LD "%s" property "%s" is %s, "%s" expected', $type, $property, json_encode($actualValue, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), $expectedValue),
                    $this->minkContext->getSession()->getDriver(),
                );
            }
        }
    }

    #[Then('the JSON-LD :type property :property should match :regex')]
    public function assertJsonLdPropertyMatches(string $type, string $property, string $regex): void
    {
        $actualValue = $this->findJsonLd($type)[$property] ?? null;

        if (!is_string($actualValue) || !preg_match($regex, $actualValue)) {
            throw new ExpectationException(
                sprintf('JSON-LD "%s" property "%s" is %s, it does not match "%s"', $type, $property, json_encode($actualValue, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), $regex),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    #[Then('the response should not contain a JSON-LD :type')]
    public function assertNoJsonLd(string $type): void
    {
        if (null !== $this->findJsonLd($type)) {
            throw new ExpectationException(sprintf('A JSON-LD of type "%s" was found', $type), $this->minkContext->getSession()->getDriver());
        }
    }

    /**
     * Décode chaque <script type="application/ld+json"> de la page : un JSON invalide, ou deux JSON-LD
     * du même type (par exemple un bloc en double après un merge), font échouer le step.
     *
     * @return array<string, mixed>|null
     */
    private function findJsonLd(string $type): ?array
    {
        $scripts = $this->minkContext->getSession()->getPage()->findAll('css', 'script[type="application/ld+json"]');

        $found = null;
        foreach ($scripts as $script) {
            $data = json_decode($script->getHtml(), true, flags: JSON_THROW_ON_ERROR);
            if (is_array($data) && ($data['@type'] ?? null) === $type) {
                if (null !== $found) {
                    throw new ExpectationException(sprintf('Several JSON-LD of type "%s" found', $type), $this->minkContext->getSession()->getDriver());
                }
                $found = $data;
            }
        }

        return $found;
    }

    #[Then('the JSON-LD :type property :property should contain :value')]
    public function assertJsonLdPropertyContains(string $type, string $property, string $value): void
    {
        $actualValue = $this->findJsonLd($type)[$property] ?? null;

        if (!is_array($actualValue) || !in_array($value, $actualValue, true)) {
            throw new ExpectationException(
                sprintf('JSON-LD "%s" property "%s" is %s, it does not contain "%s"', $type, $property, json_encode($actualValue, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), $value),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    /**
     * Le texte de chaque élément trouvé par le sélecteur CSS, dans l'ordre de la page : ni plus, ni moins.
     */
    #[Then('/^the texts of "(?P<selector>[^"]+)" should be, in this order:$/')]
    public function assertElementTextsInOrder(string $selector, PyStringNode $expectedTexts): void
    {
        $expected = array_values(array_filter(array_map(trim(...), $expectedTexts->getStrings())));
        $actual = array_map(
            static fn(NodeElement $element): string => trim($element->getText()),
            $this->minkContext->getSession()->getPage()->findAll('css', $selector),
        );

        if ($expected !== $actual) {
            throw new ExpectationException(
                sprintf('The texts of "%s" are %s, %s expected', $selector, json_encode($actual, JSON_UNESCAPED_UNICODE), json_encode($expected, JSON_UNESCAPED_UNICODE)),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    /**
     * La propriété :key de chaque élément de la liste :property du JSON-LD, dans l'ordre : ni plus, ni moins.
     */
    #[Then('the :key of each item of the JSON-LD :type property :property should be, in this order:')]
    public function assertJsonLdListValues(string $key, string $type, string $property, PyStringNode $expectedValues): void
    {
        $items = $this->findJsonLd($type)[$property] ?? null;
        $actual = is_array($items) ? array_map(static fn($item): mixed => is_array($item) ? $item[$key] ?? null : null, $items) : null;
        $expected = array_values(array_filter(array_map(trim(...), $expectedValues->getStrings())));

        if ($expected !== $actual) {
            throw new ExpectationException(
                sprintf('The "%s" of the JSON-LD "%s" property "%s" are %s, %s expected', $key, $type, $property, json_encode($actual, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), json_encode($expected, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    /**
     * Visite chaque lien de la page courante dont le href correspond au motif : la page courante change.
     */
    #[Then('/^every link matching "(?P<pattern>(?:[^"]|\\")*)" should respond with status (?P<code>\d+)$/')]
    public function assertEveryLinkMatchingRespondsWithStatus(string $pattern, int $code): void
    {
        $session = $this->minkContext->getSession();

        $urls = [];
        foreach ($session->getPage()->findAll('css', 'a[href]') as $link) {
            $href = (string) $link->getAttribute('href');
            if (preg_match($pattern, $href)) {
                $urls[$href] = true;
            }
        }

        if ($urls === []) {
            throw new ExpectationException(sprintf('No link matching "%s" was found', $pattern), $session->getDriver());
        }

        $failures = [];
        foreach (array_keys($urls) as $url) {
            $this->minkContext->visitPath($url);
            $status = $session->getStatusCode();
            if ($status !== $code) {
                $failures[] = sprintf('%s (%d)', $url, $status);
            }
        }

        if ($failures !== []) {
            throw new ExpectationException(
                sprintf('%d link(s) matching "%s" did not respond with status %d: %s', count($failures), $pattern, $code, implode(', ', $failures)),
                $session->getDriver(),
            );
        }
    }
}
