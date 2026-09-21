<?php

declare(strict_types=1);

namespace Afup\Tests\Support\Github;

use AppBundle\Event\Model\GithubUser;
use AppBundle\Github\Exception\UnableToFindGithubUserException;
use AppBundle\Github\GithubClient;
use Symfony\Component\HttpClient\MockHttpClient;

final class FakeGithubClient extends GithubClient
{
    /**
     * @param array<string, array<string, mixed>> $apiDataByLogin Données API simulées indexées par login GitHub
     */
    public function __construct(private readonly array $apiDataByLogin = self::DEFAULT_API_DATA)
    {
        parent::__construct(new MockHttpClient());
    }

    private const array DEFAULT_API_DATA = [
        'amenophis' => [
            'login' => 'amenophis',
            'id' => 189419,
            'avatar_url' => 'https://avatars.githubusercontent.com/u/189419?v=4',
            'company' => 'AFUP',
            'name' => 'Jérémy Leherpeur',
            'html_url' => 'https://github.com/amenophis',
        ],
        'vinceAmstoutz' => [
            'login' => 'vinceAmstoutz',
            'id' => 6927270,
            'avatar_url' => 'https://avatars.githubusercontent.com/u/6927270?v=4',
            'company' => 'AFUP',
            'name' => 'Vincent Amstoutz (afup)',
            'html_url' => 'https://github.com/vinceAmstoutz',
        ],
    ];

    #[\Override]
    public function getUserInfos($username): GithubUser
    {
        if (!isset($this->apiDataByLogin[$username])) {
            throw new UnableToFindGithubUserException($username);
        }

        return GithubUser::fromApi($this->apiDataByLogin[$username]);
    }
}
