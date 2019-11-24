<?php

namespace AppBundle\Controller\Admin\Members;

use AppBundle\Association\Form\BadgeType;
use AppBundle\Event\Model\Badge;
use AppBundle\Event\Model\Repository\BadgeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BadgesController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->render('admin/members/badges/index.html.twig', [
            'title' => 'Badges',
            'badges' => $this->get('ting')->get(BadgeRepository::class)->getAll(),
        ]);
    }

    public function newAction(Request $request)
    {
        $form  = $this->createForm(BadgeType::class);

        $form->handleRequest($request);

        $badgesRepository = $this->get('ting')->get(BadgeRepository::class);

        if ($form->isValid()) {
            $data = $form->getData();

            $filename = uniqid('badge_');

            $data['image']->move(
                $this->prepareUploadedFilesDir(),
                $filename
            );

            $badge = new Badge();
            $badge->setLabel($data['label']);
            $badge->setUrl($filename);

            $badgesRepository->save($badge);

            $this->addFlash('notice', 'Le badge a été ajouté');

            return $this->redirectToRoute('admin_members_badges_index');
        }

        return $this->render('admin/members/badges/new.html.twig', [
            'title' => 'Badges',
            'form' => $form->createView(),
        ]);
    }

    private function prepareUploadedFilesDir()
    {
        $dir = $this->getParameter('kernel.project_dir') . '/htdocs/uploads/badges';

        if (!is_dir($dir)) {
            mkdir($dir);
        }

        return $dir;
    }
}
