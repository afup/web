<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\GithubUser;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Event\Form\GithubUserFormData;
use AppBundle\Event\Form\GithubUserType;
use AppBundle\Event\Model\Repository\GithubUserRepository;
use AppBundle\Github\GithubClient;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class GithubUserAddAction
{
    use DbLoggerTrait;

    private GithubUserRepository $githubUserRepository;
    private FormFactoryInterface $formFactory;
    private FlashBagInterface $flashBag;
    private UrlGeneratorInterface $urlGenerator;
    private Environment $twig;
    private GithubClient $githubClient;

    public function __construct(
        GithubUserRepository  $githubUserRepository,
        GithubClient          $githubClient,
        FormFactoryInterface  $formFactory,
        FlashBagInterface     $flashBag,
        UrlGeneratorInterface $urlGenerator,
        Environment           $twig
    ) {
        $this->githubUserRepository = $githubUserRepository;
        $this->githubClient = $githubClient;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        $data = new GithubUserFormData();
        $form = $this->formFactory->create(GithubUserType::class, $data, [
            'github_client' => $this->githubClient,
            'github_user_repository' => $this->githubUserRepository,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $githubUser = $data->user;
            $githubUser->setAfupCrew($data->afupCrew);
            $this->githubUserRepository->save($githubUser);

            $this->log("Ajout de l'utilisateur github {$githubUser->getLogin()}");
            $this->flashBag->add('notice', "L'utilisateur github a été ajouté");

            return new RedirectResponse($this->urlGenerator->generate('admin_github_user_list'));
        }

        return new Response($this->twig->render('admin/github_user/add.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
