<?php

declare(strict_types=1);

namespace Afup\Tests\Behat\Bootstrap;

use Behat\Mink\Exception\ExpectationException;
use Behat\Step\Then;
use Behat\Step\When;

trait FormContext
{
    #[Then('I submit the form with name :formName')]
    public function submitFormWithName(string $formName): void
    {
        $form = $this->minkContext->getSession()->getPage()->find('xpath', "//form[@name='$formName']");

        if (null === $form) {
            throw new ExpectationException(
                sprintf('The form named "%s" not found', $formName),
                $this->minkContext->getSession()->getDriver(),
            );
        }

        $form->submit();
    }

    #[Then('The :field field should only contain the follow values :expectedValuesJson')]
    public function selectHasValues(string $field, string $expectedValuesJson): void
    {
        $node = $this->minkContext->assertSession()->fieldExists($field);
        $options = $node->findAll('css', 'option');

        $expectedValues = json_decode($expectedValuesJson, true);

        $foundValues = [];
        foreach ($options as $option) {
            $foundValues[] = $option->getText();
        }

        if ($foundValues !== $expectedValues) {
            throw new ExpectationException(
                sprintf(
                    'The select has the following values %s (expected %s)',
                    json_encode($foundValues, JSON_UNESCAPED_UNICODE),
                    $expectedValuesJson,
                ),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    #[Then('The :field field should have the following selected value :expectedValue')]
    public function selectHasForCurrentSelectedValue(string $field, string $expectedValue): void
    {
        $node = $this->minkContext->assertSession()->fieldExists($field);
        $options = $node->findAll('css', 'option');

        $selectedValue = null;
        foreach ($options as $option) {
            if ($option->isSelected()) {
                $selectedValue = $option->getValue();
                break;
            }
        }

        if ($selectedValue !== $expectedValue) {
            throw new ExpectationException(
                sprintf('The select has the following value "%s" (expected "%s")', $selectedValue, $expectedValue),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    #[Then('The :field field should have the following selected text :expectedValue')]
    public function selectHasForCurrentSelectedText(string $field, string $expectedValue): void
    {
        $node = $this->minkContext->assertSession()->fieldExists($field);
        $options = $node->findAll('css', 'option');

        $selectedValue = null;
        foreach ($options as $option) {
            if ($option->isSelected()) {
                $selectedValue = $option->getText();
                break;
            }
        }

        if ($selectedValue !== $expectedValue) {
            throw new ExpectationException(
                sprintf('The select has the following text "%s" (expected "%s")', $selectedValue, $expectedValue),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    #[When('/^(?:|I )fill hidden field "(?P<field>(?:[^"]|\\")*)" with "(?P<value>(?:[^"]|\\")*)"$/')]
    #[When('/^(?:|I )fill hidden field "(?P<field>(?:[^"]|\\")*)" with:$/')]
    #[When('/^(?:|I )fill hidden field "(?P<value>(?:[^"]|\\")*)" for "(?P<field>(?:[^"]|\\")*)"$/')]
    public function fillHiddenField($field, $value): void
    {
        $this->minkContext->getSession()->getPage()
            ->find('css', 'input[name="' . $field . '"]')
            ?->setValue($value);
    }
}
