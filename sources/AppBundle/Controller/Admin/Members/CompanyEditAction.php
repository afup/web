<?php

namespace AppBundle\Controller\Admin\Members;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Association\Form\CompanyEditFormDataFactory;
use AppBundle\Association\Form\CompanyEditType;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use Assert\Assertion;
use Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class CompanyEditAction
{
    use DbLoggerTrait;

    /** @var CompanyEditFormDataFactory */
    private $companyEditFormDataFactory;
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var FlashBagInterface */
    private $flashBag;
    /** @var Environment */
    private $twig;
    /** @var CompanyMemberRepository */
    private $companyMemberRepository;
    /** @var UserRepository */
    private $userRepository;

    public function __construct(
        UserRepository $userRepository,
        CompanyMemberRepository $companyMemberRepository,
        CompanyEditFormDataFactory $companyEditFormDataFactory,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag,
        Environment $twig
    ) {
        $this->companyEditFormDataFactory = $companyEditFormDataFactory;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->twig = $twig;
        $this->companyMemberRepository = $companyMemberRepository;
        $this->userRepository = $userRepository;
    }

    public function __invoke(Request $request)
    {
        $company = $this->companyMemberRepository->get($request->query->get('id'));
        Assertion::notNull($company);
        $data = $this->companyEditFormDataFactory->fromCompany($company);
        $form = $this->formFactory->create(CompanyEditType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->companyEditFormDataFactory->toCompany($data, $company);
            try {
                $this->companyMemberRepository->save($company);
                $this->log('Modification de la personne morale ' . $company->getCompanyName());
                $this->flashBag->add('notice', 'La personne morale a été modifiée');

                return new RedirectResponse($this->urlGenerator->generate('admin_members_company_list', ['filter' => $company->getCompanyName()]));
            } catch (Exception $e) {
                $this->flashBag->add('error', 'Une erreur est survenue lors de la modification de la personne morale');
            }
        }

        return new Response($this->twig->render('admin/members/company_edit.html.twig', [
            'company' => $company,
            'users' => $this->userRepository->search('lastname', 'asc', null, $company->getId()),
            'form' => $form->createView(),
        ]));
    }
}
