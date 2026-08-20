<?php

declare(strict_types=1);

namespace AppBundle\Tests\Association\Form;

use AppBundle\Association\Form\CompanyMemberType;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\CompanyMemberInvitation;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Locale\LocaleResolver;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validation;

/**
 * Le premier membre rattaché a un statut particulier : son email est obligatoire et il est
 * toujours gestionnaire, sans que l'utilisateur puisse le changer.
 */
#[AllowMockObjectsWithoutExpectations] // TypeTestCase mocke l'EventDispatcher en interne
class CompanyMemberTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        $recaptcha = new EWZRecaptchaType(
            'public-key',
            false,
            false,
            new LocaleResolver('fr', false, new RequestStack()),
        );

        return [
            new PreloadedExtension([$recaptcha], []),
            // TypeTestCase ne la charge pas, or l'option `constraints` en dépend.
            new ValidatorExtension(Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()),
        ];
    }

    #[Test]
    public function laCaseGestionnaireDuPremierMembreEstVerrouillee(): void
    {
        $form = $this->buildForm();

        $first = $form->get('invitations')->get('0')->get('manager');

        self::assertTrue($first->isDisabled(), 'la case du premier membre doit être verrouillée');
        self::assertTrue($first->getData(), 'le premier membre est gestionnaire par défaut');
    }

    /**
     * `disabled` ne fait pas que griser la case : Symfony ignore la valeur soumise. Un POST
     * forgé ne peut donc pas retirer les droits de gestion au premier membre.
     */
    #[Test]
    public function unPostForgeNePeutPasDeverrouillerLePremierGestionnaire(): void
    {
        $data = $this->newCompanyMember();
        $form = $this->buildForm($data);

        $form->submit($this->payload(['manager' => '0']), false);

        self::assertTrue($this->invitation($data, 0)->getManager());
    }

    #[Test]
    public function lEmailDuPremierMembreEstObligatoire(): void
    {
        $form = $this->buildForm();
        $form->submit($this->payload(['email' => '']), false);

        self::assertFalse($form->isValid());
        self::assertCount(1, $form->get('invitations')->get('0')->get('email')->getErrors());
    }

    /**
     * Une ligne laissée vide ne doit ni planter le mapping — setEmail() n'accepte pas null —
     * ni créer une invitation fantôme.
     */
    #[Test]
    public function uneLigneVideEstSimplementIgnoree(): void
    {
        $data = $this->newCompanyMember();
        $form = $this->buildForm($data);

        $form->submit([
            'invitations' => [
                ['email' => 'premier@example.com'],
                ['email' => ''],
            ],
        ], false);

        self::assertCount(1, $this->invitations($data));
        self::assertSame('premier@example.com', $this->invitation($data, 0)->getEmail());
    }

    #[Test]
    public function unMembreSupplementaireRenseigneEstBienRattache(): void
    {
        $data = $this->newCompanyMember();
        $form = $this->buildForm($data);

        $form->submit([
            'invitations' => [
                ['email' => 'premier@example.com'],
                ['email' => 'second@example.com'],
            ],
        ], false);

        self::assertCount(2, $this->invitations($data));
        self::assertSame('second@example.com', $this->invitation($data, 1)->getEmail());
        self::assertFalse($this->invitation($data, 1)->getManager(), 'seul le premier est gestionnaire');
    }

    private function invitation(CompanyMember $member, int $index): CompanyMemberInvitation
    {
        $invitations = $member->getInvitations() ?? [];
        self::assertArrayHasKey($index, $invitations);

        return $invitations[$index];
    }

    /**
     * @return list<CompanyMemberInvitation>
     */
    private function invitations(CompanyMember $member): array
    {
        return array_values($member->getInvitations() ?? []);
    }

    /**
     * @param array<string, string> $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return ['invitations' => [array_merge(['email' => 'premier@example.com'], $overrides)]];
    }

    private function newCompanyMember(): CompanyMember
    {
        $member = new CompanyMember();
        $member->setInvitations([new CompanyMemberInvitation()->setManager(true)]);

        return $member;
    }

    /**
     * @return FormInterface<mixed>
     */
    private function buildForm(?CompanyMember $data = null): FormInterface
    {
        return $this->factory->create(CompanyMemberType::class, $data ?? $this->newCompanyMember());
    }
}
