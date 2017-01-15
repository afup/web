<?php


namespace AppBundle\Association\UserMembership;


class Reminder15DaysBeforeEnd extends AbstractUserReminder
{
    protected function getText()
    {
        return 'Cher membre AFUP,<br />
<p>Votre adhésion d’un an à l’AFUP approche de son terme ! Déjà presque une année que vous bénéficiez de nos tarifs
avantageux lors de nos événements et que vous soutenez activement la communauté PHP.</p> 
<p>Être membre de l’AFUP, c’est rester proche d’un réseau de développeurs dynamiques et créatifs,
c’est soutenir l’Open Source, ses valeurs et sa philosophie, c’est accompagner l’AFUP dans l’organisation d’événements
majeurs du monde PHP. Mais c’est, avant toute chose, faire partie d’une communauté conviviale, festive et accueillante,
qui ne serait pas tout à fait la même sans vous...</p>
<p>Restez avec nous ! Renouvelez dès maintenant votre adhésion, en réglant votre cotisation en ligne.
Elle prendra effet dans 2 semaines, au terme de l’adhésion actuelle. </p>
<p>La cotisation annuelle à l’association est au tarif de 25 euros. Pour prolonger votre adhésion, rendez-vous dans
le <a href="http://www.afup.org/pages/administration/index.php?page=membre_cotisation">back-office du site de l’AFUP</a>
 et cliquez sur “Ma Cotisation”. Un souci, une question ? Contactez-nous ! bonjour@afup.org</p>

<p>À bientôt !<br />
L’équipe AFUP</p>
';
    }

    protected function getSubject()
    {
        return 'Votre adhésion à l’AFUP arrive à son terme : J-15';
    }
}
