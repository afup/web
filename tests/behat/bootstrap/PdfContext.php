<?php

declare(strict_types=1);

namespace Afup\Tests\Behat\Bootstrap;

use Behat\Hook\BeforeScenario;
use Behat\Mink\Exception\ExpectationException;
use Behat\Step\Then;
use Behat\Step\When;
use Smalot\PdfParser\Parser;

trait PdfContext
{
    private array $pdfPages = [];

    #[BeforeScenario]
    public function resetPdfPages(): void
    {
        $this->pdfPages = [];
    }

    #[When('I parse the pdf downloaded content')]
    public function iParseThePdfContent(): void
    {
        $pageContent = $this->minkContext->getSession()->getPage()->getContent();

        $parser = new Parser();
        $pdf = $parser->parseContent($pageContent);
        $pages = $pdf->getPages();

        $this->pdfPages = [];
        foreach ($pages as $i => $page) {
            $this->pdfPages[++$i] = $page->getText();
        }
    }

    #[Then('The page :page of the PDF should contain :content')]
    public function thePageOfThePdfShouldContain(string $page, string $expectedContent): void
    {
        $pageContent = $this->pdfPages[$page] ?? null;

        if (!str_contains((string) $pageContent, $expectedContent)) {
            throw new ExpectationException(
                sprintf('The content "%s" was not found in the content "%s"', $expectedContent, $pageContent),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    #[Then('The page :page of the PDF should not contain :content')]
    public function thePageOfThePdfShouldNotContain(string $page, string $expectedContent): void
    {
        if (!isset($this->pdfPages[$page])) {
            throw new ExpectationException(
                sprintf('The page %d does not exists', $page),
                $this->minkContext->getSession()->getDriver(),
            );
        }

        $pageContent = $this->pdfPages[$page];

        if (str_contains($pageContent, $expectedContent)) {
            throw new ExpectationException(
                sprintf('The content "%s" was not found in the content "%s"', $expectedContent, $pageContent),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }
}
