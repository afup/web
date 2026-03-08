<?php

declare(strict_types=1);

namespace Afup\Tests\Behat\Bootstrap;

use AppBundle\Event\Model\Event;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Hook\BeforeScenario;
use Behat\Mink\Exception\ExpectationException;
use Behat\Step\Then;
use Symfony\Component\Filesystem\Filesystem;

trait EmailContext
{
    private const MAILCATCHER_URL = 'http://mailcatcher:1080';

    #[BeforeScenario('@clearAllMailInscriptionAttachments')]
    public function beforeScenarioClearAllMailInscriptionAttachments(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove(Event::getInscriptionAttachmentDir());
    }

    #[BeforeScenario('@clearAllSponsorFiles')]
    public function beforeScenarioClearAllSponsorFiles(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove(Event::getSponsorFileDir());
    }

    #[BeforeScenario('@clearEmails')]
    public function clearEmails(): void
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, self::MAILCATCHER_URL . '/messages');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        curl_exec($ch);
        if (curl_errno($ch) !== 0) {
            throw new \RuntimeException('Error : ' . curl_error($ch));
        }

        curl_close($ch);
    }

    #[Then('I should only receive the following emails:')]
    public function theFollowingEmailsShouldBeReceived(TableNode $expectedEmails): void
    {
        $expectedEmailsArray = [];
        foreach ($expectedEmails as $expectedEmail) {
            $expectedEmailsArray[] = [
                'to' => $expectedEmail['to'],
                'subject' => $expectedEmail['subject'],
            ];
        }


        $content = file_get_contents(self::MAILCATCHER_URL . '/messages');
        $decodedContent = json_decode($content, true);

        $foundEmails = [];
        foreach ($decodedContent as $mail) {
            $foundEmails[] = [
                'to' => implode(',', $mail['recipients']),
                'subject' => $mail['subject'],
            ];
        }

        if ($foundEmails !== $expectedEmailsArray) {
            throw new ExpectationException(
                sprintf(
                    'The emails are not the expected ones "%s" (expected "%s")',
                    var_export($foundEmails, true),
                    var_export($expectedEmailsArray, true),
                ),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    #[Then('the checksum of the attachment :filename of the message of id :id should be :md5sum')]
    public function theChecksumOfTheAttachmentOfTheMessageOfIdShouldBe(string $filename, string $id, string $md5sum): void
    {
        $infos = json_decode(file_get_contents(self::MAILCATCHER_URL . '/messages/' . $id . '.json'), true);

        $cid = null;
        foreach ($infos['attachments'] as $attachment) {
            if ($attachment['filename'] === $filename) {
                $cid = $attachment['cid'];
            }
        }

        if (null === $cid) {
            throw new ExpectationException(
                sprintf('Attachment with name %s not found', $filename),
                $this->minkContext->getSession()->getDriver(),
            );
        }

        $attachmentContent = file_get_contents(self::MAILCATCHER_URL . '/messages/' . $id . '/parts/' . $cid);
        $actualMd5sum = md5($attachmentContent);

        if ($actualMd5sum !== $md5sum) {
            throw new ExpectationException(
                sprintf('The md5sum of %s, if not %s (found %s)', $filename, $md5sum, $actualMd5sum),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    #[Then('the plain text content of the message of id :id should be :')]
    public function thePlainTextContentOfTheMessageOfIdShouldBe(string $id, PyStringNode $expectedContent): void
    {
        $content = file_get_contents(self::MAILCATCHER_URL . '/messages/' . $id . '.plain');
        $expectedContentString = $expectedContent->getRaw();

        $content = str_replace("\r\n", "\n", $content);

        if ($content !== $expectedContentString) {
            throw new ExpectationException(
                sprintf(
                    "The content \n%s\nis not the expected one \n%s\n",
                    var_export($content, true),
                    var_export($expectedContentString, true),
                ),
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    #[Then('I should receive an email')]
    public function iShouldReceiveAnEmail(): void
    {
        $content = file_get_contents(self::MAILCATCHER_URL . '/messages');
        $emails = json_decode($content, true);

        if (count($emails) !== 1) {
            throw new ExpectationException(
                'The email has not been received.',
                $this->minkContext->getSession()->getDriver(),
            );
        }
    }

    #[Then('the email should contain a full URL starting with :arg1')]
    public function theEmailShouldContainAFullUrlStartingWith(string $arg1): void
    {
        $content = file_get_contents(self::MAILCATCHER_URL . '/messages');
        $decodedContent = json_decode($content, true);

        $foundEmails = [];
        foreach ($decodedContent as $mail) {
            $foundEmails[] = [
                'id' => $mail['id'],
                'to' => $mail['to'] ?? $mail['recipients'][0],
                'subject' => $mail['subject'],
            ];
        }

        if (count($foundEmails) !== 1) {
            throw new ExpectationException(
                'The email has not been received.',
                $this->minkContext->getSession()->getDriver(),
            );
        }

        $content = file_get_contents(self::MAILCATCHER_URL . '/messages/' . $foundEmails[0]['id'] . '.plain');
        if (!str_contains($content, $arg1)) {
            throw new ExpectationException(
                sprintf(
                    'The email content does not contain the expected URL "%s" (expected "%s")',
                    $content,
                    $arg1,
                ), $this->minkContext->getSession()->getDriver(),
            );
        }
    }
}
