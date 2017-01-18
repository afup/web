<?php


namespace AppBundle\Association\UserMembership;


class Reminder15DaysAfterEnd extends AbstractUserReminder
{
    protected function getText()
    {
        return '<p>Bonjour,</p>

<p>Votre adhésion à l’AFUP s’est terminée il y a quelques jours… Vous nous manquez, pas vous ?</p>
<p>Votre compte est toujours accessible si vous souhaitez nous rejoindre pour une nouvelle année.
Pour cela, il vous suffit de vous rendre dans le
<a href="http://www.afup.org/pages/administration/index.php?page=membre_cotisation">back-office du site de l’AFUP</a>
et de cliquer sur “Ma Cotisation”. Une fois le règlement de ' . $this->membershipFee .' euros effectué, elle prendra effet immédiatement.
Un souci, une question ? Contactez-nous ! bonjour@afup.org </p>

<p>Si non, c’est promis, nous tournons la page sur votre adhésion mais n\'oublions pas votre engagement passé.
C\'est l\'heure pour nous de vous remercier d\'avoir été membre et nous ne vous solliciterons plus à ce sujet.</p>

<p>Espérant vous revoir très prochainement, et bonne route si ce n’est pas le cas !<br />
L’équipe AFUP</p>
';
    }

    protected function getSubject()
    {
        return 'Votre adhésion à l’AFUP : vous nous manquez !';
    }
}
