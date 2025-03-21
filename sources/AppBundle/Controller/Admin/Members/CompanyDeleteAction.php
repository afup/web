<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use Exception;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CompanyDeleteAction
{
    use DbLoggerTrait;

    private FlashBagInterface $flashBag;
    private UrlGeneratorInterface $urlGenerator;
    private CompanyMemberRepository $companyMemberRepository;

    public function __construct(
        CompanyMemberRepository $companyMemberRepository,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->companyMemberRepository = $companyMemberRepository;
    }

    public function __invoke(Request $request)
    {
        $companyMember = $this->companyMemberRepository->get($request->query->get('id'));
        if (null === $companyMember) {
            throw new NotFoundHttpException('Personne morale non trouvée');
        }
        try {
            $this->companyMemberRepository->remove($companyMember);
            $this->log('Suppression de la personne morale ' . $companyMember->getId());
            $this->flashBag->add('notice', 'La personne morale a été supprimée');
        } catch (InvalidArgumentException $e) {
            $this->flashBag->add('error', $e->getMessage());
        } catch (Exception $e) {
            $this->flashBag->add('error', 'Une erreur est survenue lors de la suppression de la personne morale');
        }

        return new RedirectResponse($this->urlGenerator->generate('admin_members_company_list'));
    }
}
