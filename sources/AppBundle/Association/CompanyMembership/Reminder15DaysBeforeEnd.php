<?php

declare(strict_types=1);


namespace AppBundle\Association\CompanyMembership;

class Reminder15DaysBeforeEnd extends AbstractCompanyReminder
{
    protected function getText(): string
    {
        return 'Cher membre AFUP,<br />
<p>Votre adhésion d’un an à l’AFUP approche de son terme ! Déjà presque une année que vous bénéficiez de nos tarifs
avantageux lors de nos évènements et que vous soutenez activement la communauté PHP.</p> 

<p>Être membre de l’AFUP, c’est permettre à vos salarié·e·s de rester proche d’un réseau de développeurs dynamiques
et créatifs, c’est soutenir l’open Source, ses valeurs et sa philosophie, c’est accompagner l’AFUP dans l’organisation
d’évènements majeurs du monde PHP. Mais c’est, avant toute chose, faire partie d’une communauté conviviale, festive et
accueillante, qui ne serait pas tout à fait la même sans vous…</p>

<p><strong>Restez avec nous !</strong> Renouvelez dès maintenant votre adhésion, en réglant votre cotisation en ligne.
Elle prendra effet dans 2 semaines, au terme de l’adhésion actuelle. </p>

<p>La cotisation annuelle à l’association pour les entreprises est au tarif de ' . $this->membershipFee . ' euros pour 3 salarié·e·s.
Pour prolonger votre adhésion, rendez-vous dans 
le <a href="https://afup.org/member/">back-office du site de l’AFUP</a>
 et cliquez sur “Ma Cotisation”. Un souci, une question ? Contactez-nous ! <a href="mailto:bonjour@afup.org">bonjour@afup.org</a></p>

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
