<?php

declare(strict_types=1);

namespace Afup\Site\Association;

use Afup\Site\Utils\Utils;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final readonly class CotisationsFactory
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private CompanyMemberRepository $companyMemberRepository,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {}

    public function create(): Cotisations
    {
        $cotisations =  new Cotisations(
            $GLOBALS['AFUP_DB'],
            Utils::fabriqueDroits($this->tokenStorage, $this->authorizationChecker),
        );

        $cotisations->setCompanyMemberRepository($this->companyMemberRepository);

        return $cotisations;
    }
}
