<?php

namespace App\RendezVous\Admin\AddAttendee;

use Afup\Site\Logger\DbLoggerTrait;
use App\Action;
use App\RendezVous\Admin\EditAttendee\EditAttendeeFormData;
use App\RendezVous\Admin\EditAttendee\EditAttendeeFormType;
use App\RendezVous\RendezVousAttendee;
use App\RendezVous\RendezVousAttendeeRepository;
use App\RendezVous\RendezVousRepository;
use App\RendezVous\RendezVousService;
use Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class AddAttendeeAction implements Action
{
    use DbLoggerTrait;

    /** @var FlashBagInterface */
    private $flashBag;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var RendezVousRepository */
    private $rendezVousRepository;
    /** @var Environment */
    private $twig;
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var RendezVousService */
    private $rendezVousService;
    /** @var RendezVousAttendeeRepository */
    private $rendezVousAttendeeRepository;

    public function __construct(
        RendezVousRepository $rendezVousRepository,
        RendezVousAttendeeRepository $rendezVousAttendeeRepository,
        RendezVousService $rendezVousService,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        Environment $twig
    ) {
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->rendezVousRepository = $rendezVousRepository;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->rendezVousService = $rendezVousService;
        $this->rendezVousAttendeeRepository = $rendezVousAttendeeRepository;
    }

    public function __invoke(Request $request)
    {
        $id = $request->query->getInt('id');
        $data = new EditAttendeeFormData();
        $form = $this->formFactory->create(EditAttendeeFormType::class, $data);
        $rendezVous = $this->rendezVousRepository->find($id);
        if (null === $rendezVous) {
            $this->flashBag->add('error', 'Il manque l\'identifiant du rendez-vous pour effectuer l\'inscription.');

            return new RedirectResponse($this->urlGenerator->generate('admin_rendezvous_list'));
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $attendee = new RendezVousAttendee();
                $attendee->setRendezVousId($rendezVous->getId());
                $attendee->setCreation(time());
                $attendee->setPresence($this->rendezVousService->getPossiblePresence($rendezVous));
                $attendee->setLastname($data->lastname);
                $attendee->setFirstname($data->firstname);
                $attendee->setCompany($data->company);
                $attendee->setEmail($data->email);
                $attendee->setPhone($data->phone);
                $attendee->setConfirmed($data->confirmed);
                $this->rendezVousAttendeeRepository->save($attendee);
                $this->log('Enregistrement de l\'inscription au prochain rendez-vous');
                $this->flashBag->add('notice', 'L\'inscription a été enregistrée.');

                return new RedirectResponse($this->urlGenerator->generate('admin_rendezvous_list', ['id' => $rendezVous->getId()]));
            } catch (Exception $e) {
                $this->flashBag->add('error', 'Une erreur est survenue lors de l\'enregistrement de l\'inscription');
            }
        }

        return new Response($this->twig->render('admin/rendezvous/add_attendee.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
