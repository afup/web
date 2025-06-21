<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\TechLetter;

use AppBundle\TechLetter\Model\News;
use AppBundle\TechLetter\Model\Repository\SendingRepository;
use AppBundle\TechLetter\Model\TechLetterFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class HistoryAction extends AbstractController
{
    public function __construct(
        private readonly SendingRepository $sendingRepository,
        private readonly TechLetterFactory $techLetterFactory,
    ) {}

    public function __invoke(): Response
    {
        $history = [];
        foreach ($this->sendingRepository->getAll() as $sending) {
            $defaultColumns = [
                'date' => $sending->getSendingDate(),
            ];

            $techLetter = $this->techLetterFactory->createTechLetterFromJson($sending->getTechletter());

            if (($firstNews = $techLetter->getFirstNews()) instanceof News) {
                $url = $firstNews->getUrl();
                $history[] = $defaultColumns + [
                    'type' => 'First news',
                    'url' => $url,
                    'title' => $firstNews->getTitle(),
                ];
            }

            if (($secondNewsNews = $techLetter->getSecondNews()) instanceof News) {
                $url = $secondNewsNews->getUrl();
                $history[] = $defaultColumns + [
                    'type' => 'second news',
                    'url' => $url,
                    'title' => $secondNewsNews->getTitle(),
                ];
            }

            foreach ($techLetter->getArticles() as $article) {
                $history[] = $defaultColumns + [
                    'type' => 'article',
                    'url' => $article->getUrl(),
                    'title' => $article->getTitle(),
                ];
            }

            foreach ($techLetter->getProjects() as $project) {
                $history[] = $defaultColumns + [
                    'type' => 'project',
                    'url' => $project->getUrl(),
                    'title' => $project->getName(),
                ];
            }
        }

        return $this->render('admin/techletter/history.html.twig', [
            'title' => "Veille de l'AFUP",
            'history' => $history,
        ]);
    }
}
