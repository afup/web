<?php

namespace AppBundle\Controller\Website\Member;

use AppBundle\Association\Form\AdminCompanyMemberType;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\User;
use AppBundle\Controller\Website\BlocksHandler;
use Assert\Assertion;
use Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class CompanyController
{
    /** @var CompanyMemberRepository */
    private $companyMemberRepository;
    /** @var BlocksHandler */
    private $blocksHandler;
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var FlashBagInterface */
    private $flashBag;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var Security */
    private $security;
    /** @var Environment */
    private $twig;

    public function __construct(
        CompanyMemberRepository $companyMemberRepository,
        BlocksHandler $blocksHandler,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        Security $security,
        Environment $twig
    ) {
        $this->companyMemberRepository = $companyMemberRepository;
        $this->blocksHandler = $blocksHandler;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
        $this->twig = $twig;
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

        return new Response($this->twig->render('admin/association/membership/company.html.twig', [
            'title' => 'Mon adhésion entreprise',
            'form' => $subscribeForm->createView(),
        ] + $this->blocksHandler->getDefaultBlocks()));
    }
}
