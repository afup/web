<?php

namespace AppBundle\Controller;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Indexation\Talks\Runner;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class AdminTalkController extends Controller
{
    public function exportAction(Request $request)
    {
        $event = $this->getEvent($this->get(EventRepository::class), $request);

        $file = new \SplFileObject(sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('talk_'), 'w+');
        $this->get(\AppBundle\Event\Talk\ExportGenerator::class)->export($event, $file);

        $headers = [
            'Content-Type' =>  'text/html; charset=utf-8',
            'Content-Disposition' => sprintf('attachment; filename="talks_%s_%s.csv"', $event->getPath(), date('Ymd-His')),
        ];

        $response = new BinaryFileResponse($file, BinaryFileResponse::HTTP_OK, $headers);
        $response->deleteFileAfterSend(true);
        return $response;
    }

    public function updateIndexationAction(Request $request)
    {
        $runner = new Runner($this->get(\AlgoliaSearch\Client::class), $this->get('ting'));
        $runner->run();

        $this->addFlash('notice', 'Indexation effectuée');

        return new RedirectResponse($request->headers->get('referer', '/pages/administration/index.php?page=forum_sessions'));
    }

    private function getEvent(EventRepository $eventRepository, Request $request)
    {
        $event = null;
        if ($request->query->has('id') === false) {
            $event = $eventRepository->getNextEvent();
            $event = $eventRepository->get($event->getId());
        } else {
            $id = $request->query->getInt('id');
            $event = $eventRepository->get($id);
        }

        return $event;
    }
}
