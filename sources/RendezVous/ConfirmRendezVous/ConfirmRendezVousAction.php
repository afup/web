<?php

namespace App\RendezVous\ConfirmRendezVous;

use Afup\Site\Logger\DbLoggerTrait;
use App\Action;
use App\RendezVous\RendezVousAttendee;
use App\RendezVous\RendezVousAttendeeRepository;
use App\RendezVous\RendezVousRepository;
use Assert\Assertion;
use Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ConfirmRendezVousAction implements Action
{
    use DbLoggerTrait;

    /** @var Environment */
    private $twig;
    /** @var RendezVousAttendeeRepository */
    private $rendezVousAttendeeRepository;
    /** @var RendezVousRepository */
    private $rendezVousRepository;
    /** @var FormFactoryInterface */
    private $formFactory;

    public function __construct(
        FormFactoryInterface $formFactory,
        RendezVousRepository $rendezVousRepository,
        RendezVousAttendeeRepository $rendezVousAttendeeRepository,
        Environment $twig
    ) {
        $this->twig = $twig;
        $this->rendezVousAttendeeRepository = $rendezVousAttendeeRepository;
        $this->rendezVousRepository = $rendezVousRepository;
        $this->formFactory = $formFactory;
    }

    public function __invoke(Request $request)
    {
        $hash = $request->query->get('hash');
        $rendezVous = $attendee = null;
        if (null !== $hash) {
            $attendee = $this->rendezVousAttendeeRepository->findOneByHash($hash);
            if (null !== $attendee && null !== $attendee->getRendezVousId()) {
                $rendezVous = $this->rendezVousRepository->find($attendee->getRendezVousId());
                Assertion::greaterThan($rendezVous->getStart(), time());
            }
        }
        if (null === $rendezVous) {
            $rendezVous = $this->rendezVousRepository->findNext();
        }

        if (null === $rendezVous) {
            return new Response($this->twig->render('legacy/rendezvous/pas-de-rendezvous.html.twig'));
        }

        if (null === $attendee) {
            return new Response($this->twig->render('legacy/rendezvous/message.html.twig', [
                'resultat' => 'erreur',
                'message' => 'La confirmation n\'est pas possible. N\'avez-vous pas déjà précisé que vous ne veniez pas ?',
                'rendezVous' => $rendezVous,
            ]));
        }
        $data = new ConfirmRendezVousFormData();
        $data->lastname = $attendee->getLastname();
        $data->firstname = $attendee->getFirstname();
        $data->company = $attendee->getCompany();
        $data->email = $attendee->getEmail();
        $data->phone = $attendee->getPhone();
        $data->confirmed = $attendee->getConfirmed();
        $data->presence = $attendee->getPresence();
        $form = $this->formFactory->create(ConfirmRendezVousFormType::class, $data);
        $form->handleRequest($request);
        $params = [];
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $attendee->setConfirmed($data->confirmed);
                switch ($attendee->getConfirmed()) {
                    case RendezVousAttendee::CONFIRMED:
                        $attendee->setPresence(RendezVousAttendee::COMING);
                        break;
                    case RendezVousAttendee::DECLINED:
                        $attendee->setPresence(RendezVousAttendee::REFUSED);
                        break;
                    default:
                        $attendee->setPresence($data->presence);
                }
                $attendee->setLastname($data->lastname);
                $attendee->setFirstname($data->firstname);
                $attendee->setCompany($data->company);
                $attendee->setEmail($data->email);
                $attendee->setPhone($data->phone);
                $this->rendezVousAttendeeRepository->save($attendee);
                $this->log('Confirmation pour le prochain rendez-vous de '.$data->lastname);

                return new Response($this->twig->render('legacy/rendezvous/message.html.twig', [
                    'resultat' => 'succes',
                    'message' => 'Votre confirmation a bien été prise en compte.',
                    'rendezVous' => $rendezVous,
                ]));
            } catch (Exception $e) {
                $params['resultat'] = 'erreur';
                $params['message'] = 'Il y a une erreur lors de votre confirmation. Merci de bien vouloir recommencer.';
            }
        }

        return new Response($this->twig->render('legacy/rendezvous/confirmation.html.twig', [
                'form' => $form->createView(),
                'rendezVous' => $rendezVous,
            ] + $params));
    }
}
