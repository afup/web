<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\Form\EventThemeType;
use AppBundle\Event\Model\EventTheme;
use AppBundle\Event\Model\Repository\EventThemeRepository;
use CCMBenchmark\TingBundle\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EventThemeAddEditAction extends AbstractController
{
    public function __construct(
        private readonly EventThemeRepository $eventThemeRepository,
    ) {}

    public function __invoke(Request $request, #[MapEntity] ?EventTheme $eventTheme = null): Response
    {
        $new = false;
        if ($eventTheme === null) {
            $new = true;
            $eventTheme = new EventTheme();
            if ($request->query->has('idForum')) {
                $eventTheme->setIdForum($request->query->getInt('idForum'));
            }
        }

        $form = $this->createForm(EventThemeType::class, $eventTheme);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dump($eventTheme);
            $this->eventThemeRepository->save($eventTheme);

            $this->addFlash('notice', 'Thème ' . ($new ? 'ajouté' : 'modifié'));
            return $this->redirectToRoute('admin_event_themes_list', ['id' => $eventTheme->getIdForum()]);
        }

        return $this->render('admin/event/theme_add_edit.html.twig', [
            'form' => $form->createView(),
            'eventTheme' => $eventTheme,
            'new' => $new
        ]);
    }
}
