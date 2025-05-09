<?php

declare(strict_types=1);

namespace AppBundle\Github;

use AppBundle\Event\Model\GithubUser;
use AppBundle\Github\Exception\UnableToFindGithubUserException;
use AppBundle\Github\Exception\UnableToGetGithubUserInfosException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GithubClient
{
    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    /**
     * @param string $username
     *
     * @throws UnableToFindGithubUserException
     * @throws UnableToGetGithubUserInfosException
     */
    public function getUserInfos($username): GithubUser
    {
        $response = $this->httpClient->request('GET', "/users/$username", [
            'headers' => [
                'User-Agent' => 'afup',
            ],
        ]);

        if ($response->getStatusCode() === 404) {
            throw new UnableToFindGithubUserException($username);
        }

        if ($response->getStatusCode() === 200) {
            return GithubUser::fromApi($response->toArray());
        }

        throw new UnableToGetGithubUserInfosException(
            $response->getStatusCode(),
            $response->getContent()
        );
    }
}
