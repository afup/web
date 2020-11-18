<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface Action
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request);
}
