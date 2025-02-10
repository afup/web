<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Planete;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Planete\FeedFormData;
use AppBundle\Planete\FeedFormType;
use PlanetePHP\FeedRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class FeedAddAction
{
    use DbLoggerTrait;

    private FeedRepository $feedRepository;
    private FormFactoryInterface $formFactory;
    private FlashBagInterface $flashBag;
    private UrlGeneratorInterface $urlGenerator;
    private Environment $twig;

    public function __construct(
        FeedRepository $feedRepository,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        Environment $twig
    ) {
        $this->feedRepository = $feedRepository;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        $data = new FeedFormData();
        $form = $this->formFactory->create(FeedFormType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ok = $this->feedRepository->insert(
                $data->name,
                $data->url,
                $data->feed,
                $data->status,
                $data->userId
            );

            if ($ok) {
                $this->log('Ajout du flux ' . $data->name);
                $this->flashBag->add('notice', 'Le flux a été ajouté');

                return new RedirectResponse($this->urlGenerator->generate('admin_planete_feed_list'));
            }
            $this->flashBag->add('error', 'Une erreur est survenue lors de l\'ajout du flux');
        }

        return new Response($this->twig->render(':admin/planete:feed_add.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
