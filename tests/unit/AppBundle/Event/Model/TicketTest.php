<?php

declare(strict_types=1);

namespace AppBundle\Tests\Event\Model;

use AppBundle\Event\Model\Ticket;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class TicketTest extends TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    #[DataProvider('emailProvider')]
    public function testEmailIsValidated(string $email, bool $isValid): void
    {
        $ticket = new Ticket();
        $ticket->setEmail($email);

        $violations = $this->validator->validateProperty($ticket, 'email');

        if ($isValid) {
            self::assertCount(0, $violations);
        } else {
            self::assertCount(1, $violations);
            self::assertSame($email, $violations[0]->getInvalidValue());
        }
    }

    public static function emailProvider(): iterable
    {
        yield 'valid email with TLD' => ['john.doe@example.com', true];
        yield 'valid email with subdomain' => ['john.doe@mail.example.com', true];
        yield 'email without TLD is refused' => ['john@hostname', false];
        yield 'email with empty domain is refused' => ['john@', false];
        yield 'email without arobase is refused' => ['john.doe', false];
        yield 'empty email is refused' => ['', false];
    }
}
