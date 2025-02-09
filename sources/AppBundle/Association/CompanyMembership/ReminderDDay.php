<?php

declare(strict_types=1);


namespace AppBundle\Association\CompanyMembership;

class ReminderDDay extends AbstractCompanyReminder
{
    protected function getText(): string
    {
        return '<p>Cher société membre de l\'AFUP,</p>

<p>Il y a un an, vous adhériez à l’AFUP ! Votre adhésion annuelle est désormais arrivée à son terme aujourd’hui...
Mais n’en restons pas là, prolongeons l’aventure ensemble ! Renouvelez dès maintenant votre adhésion pour une nouvelle
année. <br />
Une année de tarifs avantageux pour permettre à vos développeurs de progresser, d’échanges quotidiens avec la
communauté via les mailing-lists, et une année de soutien majeur à une communauté PHP dynamique, créative,
conviviale et soudée !</p>

<p><strong>Renouvelez dès maintenant votre adhésion</strong>, en réglant votre cotisation en ligne. Elle prendra effet immédiatement. </p>

<p>La cotisation à l’association  est au tarif de ' . $this->membershipFee . ' euros pour ' . $this->membersPerFee . ' salarié·e·s.
Pour prolonger votre adhésion, rendez-vous dans le 
<a href="https://afup.org/pages/administration/index.php?page=membre_cotisation">back-office du site de l’AFUP</a>
et cliquez sur “Ma Cotisation”. Un souci, une question ? Contactez-nous ! <a href="mailto:bonjour@afup.org">bonjour@afup.org</a></p>

<p>À bientôt !<br />
L’équipe AFUP</p>
';
    }

    protected function getSubject(): string
    {
        return 'Fin de validité de votre adhésion à l’AFUP';
    }

    protected function getKey(): string
    {
        return 'DDay';
    }
}
