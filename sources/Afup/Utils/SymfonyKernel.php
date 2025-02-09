<?php

declare(strict_types=1);


namespace Afup\Site\Utils;

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class SymfonyKernel
{
    protected \AppKernel $kernel;
    protected ?Request $request;
    protected ?Response $response = null;

    public function __construct(Request $request = null)
    {
        $env = 'prod';
        $debug = false;

        if (isset($_ENV['SYMFONY_ENV']) && $_ENV['SYMFONY_ENV'] === 'dev') {
            Debug::enable(E_WARNING);
            $debug = true;
            $env = 'dev';
        }

        if (isset($_ENV['SYMFONY_ENV']) && $_ENV['SYMFONY_ENV'] === 'test') {
            $env = 'test';
        }

        $this->kernel = new \AppKernel($env, $debug);
        $this->kernel->boot();
        if (!$request instanceof Request) {
            $request = Request::createFromGlobals();
        }
        $this->request = $request;
    }

    private function handleRequest(?string $uri = null): void
    {
        if (!$this->response instanceof Response) {
            $server = $_SERVER;
            if ($uri !== null) {
                $_SERVER['REQUEST_URI'] = $uri;
                $_SERVER['AFUP_CONTEXT'] = true;
            }
            $this->response = $this->kernel->handle($this->request);
            $_SERVER = $server;
        }
    }

    public function getKernel(): KernelInterface
    {
        return $this->kernel;
    }

    public function getResponse(): Response
    {
        $this->handleRequest();
        return $this->response;
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    public function getToken(): ?string
    {
        if (!$this->response instanceof Response) {
            return null;
        }
        return $this->response->headers->get('X-Debug-Token');
    }

    public function getRequest(string $uri): ?Request
    {
        $this->handleRequest($uri);
        return $this->request;
    }
}
