<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members;

use AppBundle\Association\Form\BadgeType;
use AppBundle\Event\Model\Badge;
use AppBundle\Event\Model\Repository\BadgeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class BadgeNewAction extends AbstractController
{
    public function __construct(
        private readonly BadgeRepository $badgeRepository,
        private readonly Filesystem $filesystem,
        #[Autowire('%app.badge_dir%')]
        private readonly string $storageDir,
    ) {}

    public function __invoke(Request $request): Response
    {
        $form = $this->createForm(BadgeType::class);
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
            $this->addFlash('notice', 'Le badge a été ajouté');

            return  $this->redirectToRoute('admin_members_badges_index');
        }

        return $this->render('admin/members/badges/new.html.twig', [
            'title' => 'Badges',
            'form' => $form->createView(),
        ]);
    }
}
