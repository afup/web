<?php

declare(strict_types=1);


namespace AppBundle\Association\UserMembership;

class Reminder7DaysBeforeEnd extends AbstractUserReminder
{
    protected function getText(): string
    {
        return '<p>Cher membre AFUP,</p>

<p>Votre adhésion d’un an à l’AFUP est désormais très proche de son terme : plus que 7 jours avant la fin de sa validité.</p>

<p>Durant une année, vous avez pu profiter des différentes mailing-lists mises en place par l’association,
et vous avez pu bénéficier des tarifs spéciaux “membres AFUP” lors de nos évènements. Mais avant toute chose,
par votre adhésion, vous avez activement participé au dynamisme de la communauté PHP, vous avez permis à des cycles de
conférences majeurs d’exister, et vous avez soutenu les valeurs et la philosophie de notre association.</p>

<p>Restez avec nous ! De beaux projets sont en cours à l’AFUP, et bien sûr un AFUP Day et un Forum PHP sont à venir
dans les mois prochains. Renouvelez dès maintenant votre adhésion, en réglant votre cotisation en ligne. Elle prendra
effet dans 7 jours, au terme de l’adhésion actuelle. </p>

<p>La cotisation annuelle à l’association est au tarif de ' . $this->membershipFee . ' euros.
Pour prolonger votre adhésion, rendez-vous dans votre <a href="https://afup.org/member/">espace membre du site de l’AFUP</a>.

Un souci, une question ? Contactez-nous ! bonjour@afup.org </p>

<p>À bientôt !<br />
L’équipe AFUP</p>
';
    }

    protected function getSubject(): string
    {
        return 'Votre adhésion à l’AFUP arrive à son terme : J-7 !';
    }

    protected function getKey(): string
    {
        return '7DaysBefore';
    }
}
