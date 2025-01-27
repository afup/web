<?php

declare(strict_types=1);


namespace AppBundle\Association\CompanyMembership;

class Reminder7DaysBeforeEnd extends AbstractCompanyReminder
{
    protected function getText(): string
    {
        return '<p>Chère société membre de l’AFUP,</p>

<p>Votre adhésion d’un an à l’AFUP est désormais très proche de son terme : plus que 7 jours avant la fin de sa validité.</p>

<p>Durant une année, vos salarié·e·s bénéficiant de votre adhésion ont pu profiter des différentes mailing-lists
mises en place par l’association, et vous avez pu bénéficier des tarifs spéciaux “membres AFUP” lors de nos évènements,
qui ont permis à vos équipes de se perfectionner. Mais avant toute chose, par votre adhésion, vous avez activement
participé au dynamisme de la communauté PHP, vous avez permis à des cycles de conférences majeurs d’exister et vous avez
soutenu les valeurs et la philosophie de notre association.</p>

<p><strong>Restez avec nous !</strong> De beaux projets sont en cours à l’AFUP et bien sûr un AFUP Day et un Forum PHP
sont à venir dans les mois prochains. Renouvelez dès maintenant votre adhésion, en réglant votre cotisation en ligne.
Elle prendra effet dans 7 jours, au terme de l’adhésion actuelle. 
</p>

<p>La cotisation annuelle à l’association pour les entreprises est au tarif de ' . $this->membershipFee . ' euros pour
' . $this->membersPerFee . ' salarié·e·s. Pour prolonger votre adhésion, rendez-vous dans votre 
<a href="https://afup.org/member/">espace membre du site de l’AFUP</a>.
Un souci, une question ? Contactez-nous ! <a href="mailto:bonjour@afup.org">bonjour@afup.org</a></p>

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
