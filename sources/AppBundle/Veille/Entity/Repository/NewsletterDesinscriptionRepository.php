<?php

declare(strict_types=1);

namespace AppBundle\Veille\Entity\Repository;

use AppBundle\Veille\Entity\NewsletterDesinscription;
use AppBundle\Association\Model\User;
use AppBundle\Doctrine\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<NewsletterDesinscription>
 */
class NewsletterDesinscriptionRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsletterDesinscription::class);
    }

    public function createFromWebhookData(array $data): NewsletterDesinscription
    {
        $entity = new NewsletterDesinscription();
        $entity->email = $data['email'];
        $entity->raison = $data['reason'];
        $entity->mailchimpId = $data['id'];
        $entity->dateDesinscription = new \DateTime();
        return $entity;
    }

    public function createFromUser(User $user): NewsletterDesinscription
    {
        $entity = new NewsletterDesinscription();
        $entity->email = $user->getEmail();
        $entity->raison = 'MANUAL';
        $entity->mailchimpId = null;
        $entity->dateDesinscription = new \DateTime();
        return $entity;
    }
}
