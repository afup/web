<?php

namespace AppBundle\Controller;

use AppBundle\Event\Form\SubmitSpeakerType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SubmitSpeakerController extends EventBaseController
{
    /**
     * @param string $eventSlug
     *
     * @return Response
     */
    public function indexAction(Request $request, $eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);

        $form = $this->createForm(SubmitSpeakerType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $subject = sprintf('%s - Nouvelle suggestion de speaker', $event->getTitle());

            $content = $this->renderView(
                'event/submit-speaker/mail.txt.twig',
                [
                    'event' => $event,
                    'speaker_submission' => $data,
                ]
            );

            $this->get('app.mail')->sendSimpleMessageViaSmtp(
                $subject,
                $content,
                'conferences@afup.org'
            );

            return $this->render(
                'event/submit-speaker/submit_success.html.twig',
                [
                    'event' => $event,
                ]
            );
        }

        return $this->render(
            'event/submit-speaker/submit.html.twig',
            [
                'form' => $form->createView(),
                'event' => $event,
            ]
        );
    }
}
