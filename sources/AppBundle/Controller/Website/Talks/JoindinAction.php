<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Talks;

use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Joindin\JoindinTalk;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class JoindinAction extends AbstractController
{
    public function __construct(
        private readonly JoindinTalk $joindinTalk,
        private readonly TalkRepository $talkRepository,
    ) {}

    public function __invoke(int $id, string $slug): RedirectResponse
    {
        $talk = $this->talkRepository->get($id);

        if (null === $talk || !$talk->isDisplayedOnHistory()) {
            throw $this->createNotFoundException();
        }

        if ($talk->getSlug() !== $slug) {
            return $this->redirectToRoute('talks_joindin', ['id' => $id, 'slug' => $talk->getSlug()], Response::HTTP_MOVED_PERMANENTLY);
        }

        $stub = $this->joindinTalk->getStubFromTalk($talk);

        if (null === $stub) {
            throw $this->createNotFoundException();
        }

        return $this->redirect('https://joind.in/talk/' . $stub);
    }
}
