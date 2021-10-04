<?php

namespace AppBundle\Controller\Admin\GithubUser;

use AppBundle\Event\Model\Repository\GithubUserRepository;
use Assert\Assertion;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class GithubUserListAction
{
    const VALID_SORTS = ['name'];
    const VALID_DIRECTIONS = ['asc', 'desc'];

    /** @var GithubUserRepository */
    private $githubUserRepository;
    /** @var Environment */
    private $twig;

    public function __construct(
        GithubUserRepository $githubUserRepository,
        Environment $twig
    ) {
        $this->githubUserRepository = $githubUserRepository;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        $sort = $request->query->get('sort', 'name');
        $direction = $request->query->get('direction', 'asc');
        Assertion::inArray($sort, self::VALID_SORTS);
        Assertion::inArray($direction, self::VALID_DIRECTIONS);
        $filter = $request->query->get('filter');

        $githubUsers = $this->githubUserRepository->getAll();

        return new Response($this->twig->render('admin/github_user/list.html.twig', [
            'githubUsers' => $githubUsers,
            'nbGithubUsers' => count($githubUsers),
            'sort' => $sort,
            'direction' => $direction,
            'filter' => $filter,
        ]));
    }
}
