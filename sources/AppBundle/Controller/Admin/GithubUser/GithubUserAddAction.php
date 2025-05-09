<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\GithubUser;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Event\Form\GithubUserFormData;
use AppBundle\Event\Form\GithubUserType;
use AppBundle\Event\Model\Repository\GithubUserRepository;
use AppBundle\Github\GithubClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class GithubUserAddAction extends AbstractController
{
    use DbLoggerTrait;

    public function __construct(
        private GithubUserRepository $githubUserRepository,
        private GithubClient $githubClient,
    ) {
    }

    public function __invoke(Request $request)
    {
        $data = new GithubUserFormData();
        $form = $this->createForm(GithubUserType::class, $data, [
            'github_client' => $this->githubClient,
            'github_user_repository' => $this->githubUserRepository,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $githubUser = $data->user;
            $githubUser->setAfupCrew($data->afupCrew);
            $this->githubUserRepository->save($githubUser);

            $this->log("Ajout de l'utilisateur github {$githubUser->getLogin()}");
            $this->addFlash('notice', "L'utilisateur github a été ajouté");

            return $this->redirectToRoute('admin_github_user_list');
        }

        return $this->render('admin/github_user/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
