<?php


namespace Afup\Site\Utils;


use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class SymfonyKernel
{
    protected $kernel;
    protected $request;
    protected $response;
    protected $twig = null;

    public function __construct()
    {
        $env = 'prod';
        $debug = false;

        $configuration = $this->getLegacyConfig();

        if ($configuration['divers']['afficher_erreurs']) {
            Debug::enable(E_WARNING);
            $debug = true;
            $env = 'dev';
        }

        $this->kernel = new \AppKernel($env, $debug);
        $this->kernel->boot();
    }

    private function getLegacyConfig()
    {
        // $configuration comes from this file
        include(__DIR__ . '/../../../configs/application/config.php');

        return $configuration;
    }

    /**
     * @param string $uri
     * @return void
     */
    private function handleRequest($uri = null)
    {
        $server = $_SERVER;
        if ($uri !== null) {
            $_SERVER['REQUEST_URI'] = $uri;
            $_SERVER['AFUP_CONTEXT'] = true;
        }
        $this->request = Request::createFromGlobals();
        $this->response = $this->kernel->handle($this->request);
        $_SERVER = $server;
    }

    /**
     * @return KernelInterface
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        if ($this->response === null) {
            return null;
        }
        return $this->response->headers->get('X-Debug-Token');
    }

    /**
     * @param string $uri
     *
     * @return Request
     */
    public function getRequest($uri)
    {
        $this->handleRequest($uri);
        return $this->request;
    }
}
