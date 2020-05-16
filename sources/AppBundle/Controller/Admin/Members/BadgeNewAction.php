<?php

namespace AppBundle\Controller\Admin\Members;

use AppBundle\Association\Form\BadgeType;
use AppBundle\Event\Model\Badge;
use AppBundle\Event\Model\Repository\BadgeRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class BadgeNewAction
{
    /** @var BadgeRepository */
    private $badgeRepository;
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var FlashBagInterface */
    private $flashBag;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var Environment */
    private $twig;
    /** @var string */
    private $storageDir;
    /** @var Filesystem */
    private $filesystem;

    public function __construct(
        BadgeRepository $badgeRepository,
        Filesystem $filesystem,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        Environment $twig,
        $storageDir
    ) {
        $this->badgeRepository = $badgeRepository;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
        $this->storageDir = $storageDir;
        $this->filesystem = $filesystem;
    }

    public function __invoke(Request $request)
    {
        $form = $this->formFactory->create(BadgeType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $filename = uniqid('badge_', true);
            $this->filesystem->mkdir($this->storageDir, 0755);
            $data['image']->move($this->storageDir, $filename);

            $badge = new Badge();
            $badge->setLabel($data['label']);
            $badge->setUrl($filename);
            $this->badgeRepository->save($badge);
            $this->flashBag->add('notice', 'Le badge a été ajouté');

            return  new RedirectResponse($this->urlGenerator->generate('admin_members_badges_index'));
        }

        return new Response($this->twig->render('admin/members/badges/new.html.twig', [
            'title' => 'Badges',
            'form' => $form->createView(),
        ]));
    }
}
