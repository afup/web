<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\CompanyPublicProfile;

use AppBundle\Antennes\Entity\Antenne;
use AppBundle\Antennes\Entity\Repository\AntenneRepository;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\UserMembership\BadgesComputer;
use AppBundle\Twig\ViewRenderer;
use Symfony\Component\HttpFoundation\Response;

final class IndexAction extends CompanyPublicProfileController
{
    public function __construct(
        CompanyMemberRepository $companyMemberRepository,
        private readonly ViewRenderer $view,
        private readonly BadgesComputer $badgesComputer,
        private readonly AntenneRepository $antenneRepository,
    ) {
        parent::__construct($companyMemberRepository);
    }

    public function __invoke(int $id, string $slug): Response
    {
        $companyMember = $this->checkAndGetCompanyMember($id, $slug);

        return $this->view->render('site/company_public_profile.html.twig', [
            'company_member' => $companyMember,
            'antennes' => $this->getRelatedAfupAntennes($companyMember),
            'badges' => $this->badgesComputer->getCompanyBadges($companyMember),
        ]);
    }

    /**
     * @return list<Antenne>
     */
    private function getRelatedAfupAntennes(CompanyMember $companyMember): array
    {
        $antennes = [];
        foreach ($companyMember->getFormattedRelatedAfupOffices() as $localOffice) {
            $antenne = $this->antenneRepository->findByCode($localOffice);
            if ($antenne->masqueePageAntennes) {
                continue;
            }

            $antennes[] = $antenne;
        }

        usort($antennes, fn(Antenne $a, Antenne $b): int => $a->libelle <=> $b->libelle);

        return $antennes;
    }
}
