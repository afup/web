<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Member;

use AppBundle\Association\Form\AdminCompanyMemberType;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\User;
use AppBundle\Twig\ViewRenderer;
use Assert\Assertion;
use Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class CompanyController
{
    private CompanyMemberRepository $companyMemberRepository;
    private ViewRenderer $view;
    private FormFactoryInterface $formFactory;
    private FlashBagInterface $flashBag;
    private UrlGeneratorInterface $urlGenerator;
    private Security $security;

    public function __construct(
        CompanyMemberRepository $companyMemberRepository,
        ViewRenderer            $view,
        FormFactoryInterface    $formFactory,
        FlashBagInterface       $flashBag,
        UrlGeneratorInterface   $urlGenerator,
        Security                $security
    ) {
        $this->companyMemberRepository = $companyMemberRepository;
        $this->view = $view;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
    }

    public function __invoke(Request $request)
    {
        /** @var User $user */
        $user = $this->security->getUser();
        Assertion::isInstanceOf($user, User::class);
        $company = $this->companyMemberRepository->get($user->getCompanyId());
        if ($company === null) {
            throw new NotFoundHttpException('Company not found');
        }

        $subscribeForm = $this->formFactory->create(AdminCompanyMemberType::class, $company);
        $subscribeForm->handleRequest($request);

        if ($subscribeForm->isSubmitted() && $subscribeForm->isValid()) {
            /** @var CompanyMember $member */
            $member = $subscribeForm->getData();
            try {
                $this->companyMemberRepository->save($member);
                $this->flashBag->add('notice', 'Les modifications ont bien été enregistrées.');
            } catch (Exception $exception) {
                $this->flashBag->add('error', 'Une erreur est survenue. Merci de nous contacter.');
            }

            return new RedirectResponse($this->urlGenerator->generate('member_company'));
        }

        return $this->view->render('admin/association/membership/company.html.twig', [
            'title' => 'Mon adhésion entreprise',
            'form' => $subscribeForm->createView(),
        ]);
    }
}
