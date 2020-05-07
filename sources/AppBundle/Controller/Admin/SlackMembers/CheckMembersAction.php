<?php

namespace AppBundle\Controller\Admin\SlackMembers;

use AppBundle\Slack\UsersChecker;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class CheckMembersAction
{
    /** @var UsersChecker */
    private $usersChecker;
    /** @var Environment */
    private $twig;

    public function __construct(
        UsersChecker $usersChecker,
        Environment $twig
    ) {
        $this->usersChecker = $usersChecker;
        $this->twig = $twig;
    }

    public function __invoke()
    {
        return new Response($this->twig->render('admin/slackmembers/index.html.twig', [
            'title' => 'Slack membres',
            'techletters' => [],
            'results' => $this->usersChecker->checkUsersValidity(),
        ]));
    }
}
