<?php

namespace App\RendezVous\Admin\PrepareRendezVous;

use Afup\Site\Logger\DbLoggerTrait;
use App\Action;
use App\RendezVous\RendezVous;
use App\RendezVous\RendezVousRepository;
use App\RendezVous\RendezVousSlide;
use App\RendezVous\RendezVousSlideRepository;
use Assert\Assertion;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class PrepareRendezVousAction implements Action
{
    use DbLoggerTrait;

    /** @var RendezVousRepository */
    private $rendezVousRepository;
    /** @var FlashBagInterface */
    private $flashBag;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var Environment */
    private $twig;
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var RendezVousSlideRepository */
    private $rendezVousSlideRepository;

    public function __construct(
        RendezVousRepository $rendezVousRepository,
        RendezVousSlideRepository $rendezVousSlideRepository,
        FlashBagInterface $flashBag,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator,
        Environment $twig
    ) {
        $this->rendezVousRepository = $rendezVousRepository;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->rendezVousSlideRepository = $rendezVousSlideRepository;
    }

    public function __invoke(Request $request)
    {
        $id = $request->query->getInt('id');
        $data = new PrepareRendezVousFormData();
        $existingSlides = [];
        if ($id) {
            $rendezVous = $this->rendezVousRepository->find($id);
            Assertion::notNull($rendezVous);
            $data->registration = $rendezVous->isRegistration();
            $data->place = $rendezVous->getPlace();
            $data->plan = $rendezVous->getPlan();
            $data->pitch = $rendezVous->getPitch();
            $data->theme = $rendezVous->getTheme();
            $data->title = $rendezVous->getTitle();
            $data->externalUrl = $rendezVous->getExternalUrl();
            $data->capacity = $rendezVous->getCapacity();
            $data->address = $rendezVous->getAddress();
            $data->officeId = $rendezVous->getOfficeId();
            $data->url = $rendezVous->getUrl();
            $data->date = DateTimeImmutable::createFromFormat('U', $rendezVous->getStart());
            $data->start = date('H:i', $rendezVous->getStart());
            $data->end = date('H:i', $rendezVous->getEnd());
            foreach ($this->rendezVousSlideRepository->findByRendezVous($rendezVous) as $slide) {
                $data->addSlideUrl($slide);
                $path = __DIR__.'/../../templates/rendezvous/slides/'.$slide->getFile();
                if (file_exists($path)) {
                    $existingSlides[] = $slide->getFile();
                }
            }
        } else {
            $rendezVous = new RendezVous();
        }

        $form = $this->formFactory->create(PrepareRendezVousFormType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $start = preg_split("/[:|h]/", (string) $data->start);
                $startHour = isset($start[0]) ? $start[0] : 0;
                $startMin = isset($start[1]) ? $start[1] : 0;
                $end = preg_split("/[:|h]/", (string) $data->end);
                $endHour = isset($end[0]) ? $end[0] : 0;
                $endMin = isset($end[1]) ? $end[1] : 0;
                $date = $data->date;
                $start = mktime($startHour, $startMin, 0, $date->format('m'), $date->format('d'), $date->format('Y'));
                $end = mktime($endHour, $endMin, 0, $date->format('m'), $date->format('d'), $date->format('Y'));
                $rendezVous->setTitle($data->title);
                $rendezVous->setTheme($data->theme);
                $rendezVous->setPitch($data->pitch);
                $rendezVous->setStart($start);
                $rendezVous->setEnd($end);
                $rendezVous->setPlace($data->place);
                $rendezVous->setAddress($data->address);
                $rendezVous->setUrl($data->url);
                $rendezVous->setPlan($data->plan);
                $rendezVous->setOfficeId($data->officeId);
                $rendezVous->setCapacity($data->capacity);
                $rendezVous->setExternalUrl($data->externalUrl ?: '');
                $rendezVous->setRegistration((bool) $data->registration);
                $this->rendezVousRepository->save($rendezVous);
                $this->rendezVousSlideRepository->deleteByRendezVous($rendezVous);
                $i = 0;
                foreach ($data->getSlides() as $slideData) {
                    $i++;
                    $name = $rendezVous->getId() * 10 + $i;
                    $slide = new RendezVousSlide();
                    $slide->setRendezVousId($rendezVous->getId());
                    $slide->setUrl($slideData['url']);
                    /** @var UploadedFile|null $file */
                    $file = $slideData['file'];
                    if (null !== $file) {
                        $file->move(__DIR__.'/../../templates/rendezvous/slides/', $name);
                        $slide->setFile($name);
                    }
                    $this->rendezVousSlideRepository->save($slide);
                }
                $this->log(sprintf('Enregistrement du rendez-vous du %s', $data->date->format('d/m/Y')));
                $this->flashBag->add('notice', 'Le rendez-vous a été enregistré.');

                return new RedirectResponse($this->urlGenerator->generate('admin_rendezvous_list', ['id' => $rendezVous->getId()]));
            } catch (Exception $e) {
                throw $e;
                $this->flashBag->add('error', 'Une erreur est survenue lors de l\'enregistrement du rendez-vous');
            }
        }

        return new Response($this->twig->render('admin/rendezvous/prepare.html.twig', [
            'form' => $form->createView(),
            'existingSlides' => $existingSlides,
        ]));
    }
}
