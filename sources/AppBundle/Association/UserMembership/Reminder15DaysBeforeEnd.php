<?php

declare(strict_types=1);


namespace AppBundle\Association\UserMembership;

class Reminder15DaysBeforeEnd extends AbstractUserReminder
{
    protected function getText(): string
    {
        return 'Cher membre AFUP,<br />
<p>Votre adhésion d’un an à l’AFUP approche de son terme ! Déjà presque une année que vous bénéficiez de nos tarifs
avantageux lors de nos évènements et que vous soutenez activement la communauté PHP.</p> 
<p>Être membre de l’AFUP, c’est rester proche d’un réseau de développeurs dynamiques et créatifs,
c’est soutenir l’Open Source, ses valeurs et sa philosophie, c’est accompagner l’AFUP dans l’organisation d’évènements
majeurs du monde PHP. Mais c’est, avant toute chose, faire partie d’une communauté conviviale, festive et accueillante,
qui ne serait pas tout à fait la même sans vous...</p>
<p>Restez avec nous ! Renouvelez dès maintenant votre adhésion, en réglant votre cotisation en ligne.
Elle prendra effet dans 2 semaines, au terme de l’adhésion actuelle. </p>
<p>La cotisation annuelle à l’association est au tarif de ' . $this->membershipFee . ' euros. Pour prolonger votre adhésion, rendez-vous dans
votre <a href="https://afup.org/member/">espace membre du site de l’AFUP</a>.
Un souci, une question ? Contactez-nous ! bonjour@afup.org</p>

<p>À bientôt !<br />
L’équipe AFUP</p>
';
    }

    protected function getSubject(): string
    {
        return 'Votre adhésion à l’AFUP arrive à son terme : J-15';
    }

    protected function getKey(): string
    {
        return '15DaysBefore';
    }
}
