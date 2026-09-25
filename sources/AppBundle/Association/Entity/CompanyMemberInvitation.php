<?php

declare(strict_types=1);

namespace AppBundle\Association\Entity;

use AppBundle\Association\Entity\Repository\CompanyMemberInvitationRepository;
use AppBundle\Association\Enum\InvitationEtat;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CompanyMemberInvitationRepository::class)]
#[ORM\Table(name: 'afup_personnes_morales_invitations')]
class CompanyMemberInvitation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(name: 'company_id', nullable: false)]
    public int $companyId;

    // Valeur par défaut : sans elle, la lecture de l'email sur une invitation fraîchement
    // instanciée (CompanyAction pré-remplit la collection) lèverait une erreur de
    // propriété non initialisée.
    #[Assert\Email]
    #[ORM\Column(nullable: false)]
    public string $email = '';

    #[ORM\Column(nullable: false)]
    public string $token = '';

    #[ORM\Column(nullable: false)]
    public bool $manager = false;

    #[ORM\Column(name: 'submitted_on', type: 'datetime', nullable: false)]
    public \DateTime $submittedOn;

    #[ORM\Column(nullable: false, enumType: InvitationEtat::class)]
    public InvitationEtat $status = InvitationEtat::EnAttente;
}
