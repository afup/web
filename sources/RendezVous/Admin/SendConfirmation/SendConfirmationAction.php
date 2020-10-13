<?php

namespace App\RendezVous\Admin\SendConfirmation;

use Afup\Site\Logger\DbLoggerTrait;
use App\Action;
use App\RendezVous\RendezVousRepository;
use App\RendezVous\RendezVousService;
use Assert\Assertion;
use Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class SendConfirmationAction implements Action
{
    use DbLoggerTrait;

    /** @var RendezVousRepository */
    private $rendezVousRepository;
    /** @var RendezVousService */
    private $rendezVousService;
    /** @var FlashBagInterface */
    private $flashBag;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var Environment */
    private $twig;
    /** @var FormFactoryInterface */
    private $formFactory;

    public function __construct(
        RendezVousRepository $rendezVousRepository,
        FlashBagInterface $flashBag,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator,
        RendezVousService $rendezVousService,
        Environment $twig
    ) {
        $this->rendezVousRepository = $rendezVousRepository;
        $this->rendezVousService = $rendezVousService;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
    }

    public function __invoke(Request $request)
    {
        $rendezVous = $this->rendezVousRepository->find($request->query->getInt('id'));
        Assertion::notNull($rendezVous);
        $data = new SendConfirmationFormData();
        $data->subject = 'AFUP : demande confirmation pour le prochain rendez-vous';
        $date = date('d/m/Y', $rendezVous->getStart());
        $debut = date('H\hi', $rendezVous->getStart());
        $fin = date('H\hi', $rendezVous->getEnd());
        $theme = strip_tags($rendezVous->getTheme());
        $place = strip_tags($rendezVous->getPlace());
        $data->body = <<<TXT
Le prochain rendez-vous AFUP approche.

On y parlera de : $theme.

Il se tiendra le $date de $debut à $fin. Pour le lieu il s'agit de : $place).

Merci de bien vouloir cliquer sur le lien ci-dessous : il vous permet de confirmer ou d'infirmer votre présence. Cette confirmation est fort utile pour les personnes sur liste d'attente.

Merci et à très bientôt !
L'équipe AFUP

TXT;

        $form = $this->formFactory->create(SendConfirmationFormType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->rendezVousService->sendRSVPs($rendezVous, $data->subject, $data->body);
                $this->log('Envoi des emails de demande de confirmation aux inscrits');
                $this->flashBag->add('notice', 'L\'envoi des emails de demande de confirmation aux inscrits pour le prochain rendez-vous a été effectué');

                return new RedirectResponse($this->urlGenerator->generate('admin_rendezvous_list', ['id' => $rendezVous->getId()]));
            } catch (Exception $e) {
                $this->flashBag->add('error', 'Une erreur est survenue lors de l\'envoi des emails de demande de confirmation aux inscrits pour le prochain rendez-vous');
            }
        }

        return new Response($this->twig->render('admin/rendezvous/send_confirmation.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
