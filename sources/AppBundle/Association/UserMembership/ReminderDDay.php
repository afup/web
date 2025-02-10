<?php

declare(strict_types=1);


namespace AppBundle\Association\UserMembership;

class ReminderDDay extends AbstractUserReminder
{
    protected function getText(): string
    {
        return '<p>Cher membre AFUP,</p>

<p>Il y a un an, vous adhériez à l’AFUP ! Votre adhésion annuelle est désormais arrivée à son terme aujourd’hui...
Mais n’en restons pas là, prolongeons l’aventure ensemble ! Renouvelez dès maintenant votre adhésion pour une nouvelle
année. <br />
Une année de tarifs avantageux, d’échanges, et de soutien majeur à une communauté PHP dynamique, créative,
conviviale et soudée ! </p>

<p>Renouvelez dès maintenant votre adhésion, en réglant votre cotisation en ligne. Elle prendra effet immédiatement. </p>

<p>La cotisation à l’association  est au tarif de ' . $this->membershipFee . ' euros. Pour prolonger votre adhésion, rendez-vous
dans votre <a href="https://afup.org/member/">espace membre du site de l’AFUP</a>.
Un souci, une question ? Contactez-nous ! bonjour@afup.org</p>

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
