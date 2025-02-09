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

class FeedEditAction
{
    use DbLoggerTrait;

    private FeedRepository $feedRepository;
    private FormFactoryInterface $formFactory;
    private UrlGeneratorInterface $urlGenerator;
    private FlashBagInterface $flashBag;
    private Environment $twig;

    public function __construct(
        FeedRepository $feedRepository,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag,
        Environment $twig
    ) {
        $this->feedRepository = $feedRepository;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        $id = $request->query->getInt('id');
        $feed = $this->feedRepository->get($id);
        $data = new FeedFormData();
        $data->name = $feed->getName();
        $data->feed = $feed->getFeed();
        $data->url = $feed->getUrl();
        $data->userId = $feed->getUserId();
        $data->status = $feed->getStatus();
        $form = $this->formFactory->create(FeedFormType::class, $data);
        $form->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted()) {
            $ok = $this->feedRepository->update(
                $id,
                $data->name,
                $data->url,
                $data->feed,
                $data->status,
                $data->userId
            );

            if ($ok) {
                $this->log(sprintf("Modification du flux %s (%d)", $data->name, $id));
                $this->flashBag->add('notice', 'Le flux a été modifié');

                return new RedirectResponse($this->urlGenerator->generate('admin_planete_feed_list'));
            }
            $this->flashBag->add('error', 'Une erreur est survenue lors de la modification du flux');
        }

        return new Response($this->twig->render('admin/planete/feed_edit.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
