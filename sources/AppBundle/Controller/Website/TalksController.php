<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\PlanningRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Joindin\JoindinComments;
use AppBundle\Joindin\JoindinTalk;
use AppBundle\Offices\OfficesCollection;
use AppBundle\Subtitles\Parser;
use AppBundle\Twig\ViewRenderer;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class TalksController extends AbstractController
{
    private ViewRenderer $view;
    private RepositoryFactory $repositoryFactory;
    private JoindinComments $joindinComments;
    private JoindinTalk $joindinTalk;
    private string $algoliaAppId;
    private string $algoliaFrontendApikey;

    public function __construct(ViewRenderer $view,
                                RepositoryFactory $repositoryFactory,
                                JoindinComments $joindinComments,
                                JoindinTalk $joindinTalk,
                                string $algoliaAppId,
                                string $algoliaFrontendApikey
    ) {
        $this->view = $view;
        $this->repositoryFactory = $repositoryFactory;
        $this->joindinComments = $joindinComments;
        $this->joindinTalk = $joindinTalk;
        $this->algoliaAppId = $algoliaAppId;
        $this->algoliaFrontendApikey = $algoliaFrontendApikey;
    }

    public function list(): Response
    {
        $officesCollection = new OfficesCollection();
        return $this->view->render('site/talks/list.html.twig', [
            'offices' => $officesCollection->getAllSortedByLabels(),
            'algolia_app_id' => $this->algoliaAppId,
            'algolia_api_key' => $this->algoliaFrontendApikey,
        ]);
    }

    /**
     * @param integer $id
     * @param string $slug
     */
    public function show($id, $slug): Response
    {
        $talk = $this->repositoryFactory->get(TalkRepository::class)->get($id);

        if (null === $talk || $talk->getSlug() != $slug || !$talk->isDisplayedOnHistory()) {
            throw $this->createNotFoundException();
        }

        $speakers = $this->repositoryFactory->get(SpeakerRepository::class)->getSpeakersByTalk($talk);
        $planning = $this->repositoryFactory->get(PlanningRepository::class)->getByTalk($talk);
        $event = $this->repositoryFactory->get(EventRepository::class)->get($planning->getEventId());
        $comments = $this->joindinComments->getCommentsFromTalk($talk);

        $parser = new Parser();
        $parsedContent = $parser->parse($talk->getTranscript());

        return $this->view->render('site/talks/show.html.twig', [
            'talk' => $talk,
            'event' => $event,
            'speakers' => $speakers,
            'comments' => $comments,
            'transcript' => $parsedContent,
        ]);
    }

    public function joindin($id, $slug): RedirectResponse
    {
        $talk = $this->repositoryFactory->get(TalkRepository::class)->get($id);

        if (null === $talk || $talk->getSlug() != $slug || !$talk->isDisplayedOnHistory()) {
            throw $this->createNotFoundException();
        }

        $stub = $this->joindinTalk->getStubFromTalk($talk);

        if (null === $stub) {
            throw $this->createNotFoundException();
        }

        return $this->redirect('https://joind.in/talk/' . $stub);
    }
}
