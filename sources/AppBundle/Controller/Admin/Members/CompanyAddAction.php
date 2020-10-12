<?php

namespace AppBundle\Controller\Admin\Members;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Association\Form\CompanyEditFormData;
use AppBundle\Association\Form\CompanyEditFormDataFactory;
use AppBundle\Association\Form\CompanyEditType;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class CompanyAddAction
{
    use DbLoggerTrait;

    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var FlashBagInterface */
    private $flashBag;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var Environment */
    private $twig;
    /** @var CompanyMemberRepository */
    private $companyMemberRepository;
    /** @var CompanyEditFormDataFactory */
    private $companyEditFormDataFactory;

    public function __construct(
        CompanyMemberRepository $companyMemberRepository,
        CompanyEditFormDataFactory $companyEditFormDataFactory,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        Environment $twig
    ) {
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
        $this->companyMemberRepository = $companyMemberRepository;
        $this->companyEditFormDataFactory = $companyEditFormDataFactory;
    }

    public function __invoke(Request $request)
    {
        $data = new CompanyEditFormData();
        $form = $this->formFactory->create(CompanyEditType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $company = new CompanyMember();
            $this->companyEditFormDataFactory->toCompany($data, $company);
            try {
                $this->companyMemberRepository->save($company);
                $this->log('Ajout de la personne morale ' . $company->getCompanyName());
                $this->flashBag->add('notice', 'La personne morale a été ajoutée');

                return new RedirectResponse($this->urlGenerator->generate('admin_members_company_list', ['filter' => $company->getCompanyName()]));
            } catch (Exception $e) {
                $this->flashBag->add('error', 'Une erreur est survenue lors de l\'ajout de la personne morale');
            }
        }

        return new Response($this->twig->render('admin/members/company_add.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
