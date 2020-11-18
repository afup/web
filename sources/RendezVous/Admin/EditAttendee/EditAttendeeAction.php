<?php

namespace App\RendezVous\Admin\EditAttendee;

use Afup\Site\Logger\DbLoggerTrait;
use App\Action;
use App\RendezVous\RendezVousAttendeeRepository;
use Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class EditAttendeeAction implements Action
{
    use DbLoggerTrait;

    /** @var FlashBagInterface */
    private $flashBag;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var Environment */
    private $twig;
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var RendezVousAttendeeRepository */
    private $rendezVousAttendeeRepository;

    public function __construct(
        RendezVousAttendeeRepository $rendezVousAttendeeRepository,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        Environment $twig
    ) {
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->rendezVousAttendeeRepository = $rendezVousAttendeeRepository;
    }

    public function __invoke(Request $request)
    {
        $id = $request->query->getInt('id');
        $attendee = $this->rendezVousAttendeeRepository->find($id);
        if (null === $attendee) {
            $this->flashBag->add('error', 'Il manque l\'identifiant du rendez-vous pour effectuer l\'inscription.');

            return new RedirectResponse($this->urlGenerator->generate('admin_rendezvous_list'));
        }
        $data = new EditAttendeeFormData();
        $data->firstname = $attendee->getFirstname();
        $data->lastname = $attendee->getLastname();
        $data->company = $attendee->getCompany();
        $data->email = $attendee->getEmail();
        $data->phone = $attendee->getPhone();
        $data->presence = $attendee->getPresence();
        $data->confirmed = $attendee->getConfirmed();
        $form = $this->formFactory->create(EditAttendeeFormType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $attendee->setPresence($data->presence);
                $attendee->setLastname($data->lastname);
                $attendee->setFirstname($data->firstname);
                $attendee->setCompany($data->company);
                $attendee->setEmail($data->email);
                $attendee->setPhone($data->phone);
                $attendee->setConfirmed($data->confirmed);
                $this->rendezVousAttendeeRepository->save($attendee);
                $this->log('Enregistrement de l\'inscription au prochain rendez-vous');
                $this->flashBag->add('notice', 'L\'inscription a été enregistrée.');

                return new RedirectResponse($this->urlGenerator->generate('admin_rendezvous_list', ['id' => $attendee->getRendezVousId()]));
            } catch (Exception $e) {
                $this->flashBag->add('error', 'Une erreur est survenue lors de l\'enregistrement de l\'inscription');
            }
        }

        return new Response($this->twig->render('admin/rendezvous/edit_attendee.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
