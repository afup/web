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

    public function __construct(Request $request = null)
    {
        $env = 'prod';
        $debug = false;

        $configuration = $this->getLegacyConfig();

        if ($configuration['divers']['afficher_erreurs']) {
            Debug::enable(E_WARNING);
            $debug = true;
            $env = 'dev';
        }

        if (isset($_ENV['SYMFONY_ENV']) && $_ENV['SYMFONY_ENV'] == 'test') {
            $env = 'test';
        }

        $this->kernel = new \AppKernel($env, $debug);
        $this->kernel->boot();
        if ($request === null) {
            $request = Request::createFromGlobals();
        }
        $this->request = $request;
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
        if ($this->response === null) {
            $server = $_SERVER;
            if ($uri !== null) {
                $_SERVER['REQUEST_URI'] = $uri;
                $_SERVER['AFUP_CONTEXT'] = true;
            }
            $this->response = $this->kernel->handle($this->request);
            $_SERVER = $server;
        }
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
        $this->handleRequest();
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
