<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Association\Form\CompanyEditType;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class CompanyAction
{
    use DbLoggerTrait;

    private CompanyMemberRepository $companyMemberRepository;
    private UserRepository $userRepository;
    private FormFactoryInterface $formFactory;
    private FlashBagInterface $flashBag;
    private UrlGeneratorInterface $urlGenerator;
    private Environment $twig;

    public function __construct(
        CompanyMemberRepository $companyMemberRepository,
        UserRepository $userRepository,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        Environment $twig
    ) {
        $this->formFactory = $formFactory;
        $this->userRepository = $userRepository;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
        $this->companyMemberRepository = $companyMemberRepository;
    }

    public function __invoke(Request $request, ?int $id)
    {
        $company = new CompanyMember();
        if ($id) {
            $company = $this->companyMemberRepository->get($id);
            if ($company === null) {
                $this->flashBag->add('error', 'Personne morale non trouvée');
                return new RedirectResponse($this->urlGenerator->generate('admin_members_company_list'));
            }
        }
        $form = $this->formFactory->create(CompanyEditType::class, $company);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->companyMemberRepository->save($company);
                $this->flashBag->add('notice', 'La personne morale a été ' . ($id ? 'modifiée' : 'ajoutée'));

                return new RedirectResponse($this->urlGenerator->generate('admin_members_company_list', ['filter' => $company->getCompanyName()]));
            } catch (Exception $e) {
                $this->flashBag->add('error', 'Une erreur est survenue lors de l\'ajout de la personne morale');
            }
        }

        return new Response($this->twig->render('admin/members/company/' . ($id ? 'edit' : 'add') . '.html.twig', [
            'form' => $form->createView(),
            'users' => $this->userRepository->search('lastname', 'asc', null, $company->getId()),
            'company' => $company,
        ]));
    }
}
