<?php

namespace AppBundle\Github;

use AppBundle\Event\Model\GithubUser;
use AppBundle\Github\Exception\UnableToFindGithubUserException;
use AppBundle\Github\Exception\UnableToGetGithubUserInfosException;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class GithubClient
{
    /**
     * @var Client
     */
    private $githubClient;

    public function __construct(Client $githubClient)
    {
        $this->githubClient = $githubClient;
    }

    /**
     * @param string $username
     *
     * @return GithubUser
     *
     * @throws UnableToFindGithubUserException
     * @throws UnableToGetGithubUserInfosException
     */
    public function getUserInfos($username)
    {
        $response = $this->githubClient->get("/users/{$username}", [
            RequestOptions::HEADERS => [
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'afup',
            ],
            RequestOptions::HTTP_ERRORS => false,
        ]);

        if ($response->getStatusCode() === 404) {
            throw new UnableToFindGithubUserException($username);
        }

        if ($response->getStatusCode() === 200) {
            return GithubUser::fromApi(json_decode($response->getBody()->getContents(), true));
        }

        throw new UnableToGetGithubUserInfosException(
            $response->getStatusCode(),
            $response->getBody()->getContents()
        );
    }
}
