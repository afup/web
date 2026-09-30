<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members;

use AppBundle\Association\Form\UserBadgeType;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Event\Entity\Repository\UserBadgeRepository;
use AppBundle\Event\Entity\UserBadge;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class UserBadgeNewAction
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly FormFactoryInterface $formFactory,
        private readonly UserBadgeRepository $userBadgeRepository,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $user = $this->userRepository->get($request->query->get('user_id'));
        $userBadgeForm = $this->formFactory->create(UserBadgeType::class, [], ['user' => $user]);
        $userBadgeForm->handleRequest($request);
        $data = $userBadgeForm->getData();

        if (
            !is_array($data)
            || !is_numeric($data['badge'] ?? null)
            || !is_numeric($data['user'] ?? null)
            || !($data['date'] ?? null) instanceof \DateTimeInterface
        ) {
            throw new \RuntimeException('Données du formulaire d\'attribution de badge invalides.');
        }

        $userBadge = new UserBadge();
        $userBadge->badgeId = (int) $data['badge'];
        $userBadge->issuedAt = \DateTimeImmutable::createFromInterface($data['date']);
        $userBadge->userId = (int) $data['user'];
        $this->userBadgeRepository->save($userBadge);

        return new RedirectResponse($request->headers->get('referer'));
    }
}
