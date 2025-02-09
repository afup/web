<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\GithubUser;

use AppBundle\Event\Model\Repository\GithubUserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class GithubUserListAction
{
    private GithubUserRepository $githubUserRepository;
    private Environment $twig;

    public function __construct(
        GithubUserRepository $githubUserRepository,
        Environment $twig
    ) {
        $this->githubUserRepository = $githubUserRepository;
        $this->twig = $twig;
    }

    public function __invoke(Request $request): Response
    {
        $githubUsers = $this->githubUserRepository->getAllOrderedByLogin();

        return new Response($this->twig->render('admin/github_user/list.html.twig', [
            'githubUsers' => $githubUsers,
            'nbGithubUsers' => count($githubUsers),
        ]));
    }
}
