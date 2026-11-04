<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\SlackMembers;

use AppBundle\Slack\UsersChecker;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_MEMBRES_READER')]
class CheckMembersAction
{
    public function __construct(
        private readonly UsersChecker $usersChecker,
        private readonly Environment $twig,
    ) {}

    public function __invoke(): Response
    {
        return new Response($this->twig->render('admin/slackmembers/index.html.twig', [
            'title' => 'Slack membres',
            'techletters' => [],
            'results' => $this->usersChecker->checkUsersValidity(),
        ]));
    }
}
