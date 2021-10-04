<?php

namespace AppBundle\Github;

use AppBundle\Event\Model\GithubUser;

class GithubClient
{
    /**
     * @param string $username
     * @return GithubUser|null
     */
    public function getUserInfos($username)
    {
        $opts = [
            "http" => [
                "method" => "GET",
                "header" => "Accept: application/json\r\n".
                    "User-Agent: afup\r\n"
            ]
        ];

        $context = stream_context_create($opts);

        $result = @file_get_contents('https://api.github.com/users/'.$username, false, $context);
        if ($result === false) {
            return null;
        }

        return GithubUser::fromApi(json_decode($result, true));
    }
}
