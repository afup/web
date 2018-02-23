<?php


namespace AppBundle\Controller;

use AppBundle\Offices\OfficesCollection;
use Symfony\Component\HttpFoundation\Request;

class StaticController extends SiteBaseController
{
    public function officesAction()
    {
        $officesCollection = new OfficesCollection();
        return $this->render(
        ':site:offices.html.twig',
            [
                'offices' => $officesCollection->getAllSortedByLabels()
            ]
        );
    }

    public function superAperoAction()
    {
        $aperos = [
            [
                'code' => 'marseille',
                'content' => <<<EOF
Infos à venir.
EOF
                ,
            ],
            [
                'code' => 'bordeaux',
                'content' => <<<EOF
Infos à venir.
EOF
            ],
            [
                'code' => 'clermont',
                'content' => <<<EOF
RDV à 19h au Delirium Café, 20 rue de la Tour d'Auvergne.
La communauté PHP est invitée à partager quelques verres et participer au quiz mettant en concurrence toutes les antennes AFUP de France.
Merci à <a href="https://corporate.domraider.com/">Domraider</a>, sponsor de la soirée clermontoise ! 

EOF
            ],
            [
                'code' => 'valence',
                'content' => <<<EOF
Infos à venir.
EOF
            ],
            [
                'code' => 'lille',
                'content' => <<<EOF
Infos à venir.
EOF
            ],
            [
                'code' => 'limoges',
                'meetup_id' => '247866159',
                'content' => <<<EOF
RDV à 20h à la face de Sciences Techniques, 123 Avenue Albert Thomas pour l’apéro communautaire en tant que tel, avec boissons et participation au quiz !
Mais si vous êtes dispos plus tôt, notez que l’apéro est précédé d’un atelier TDD Légo à 18h.
EOF
            ],
            [
                'code' => 'luxembourg',
                'content' => <<<EOF
RDV à 18h30 chez Nyuko, 121, rue de Hollerich.
L’occasion de rencontrer Renaud et Anthony qui souhaitent reprendre les rênes de l’antenne, et de se mettre en compétition avec les autres antennes AFUP lors du quiz. 
EOF
            ],
            [
                'code' => 'lorraine',
                'content' => <<<EOF
RDV à 19h au Monkey Bar, 14 rue des roches : quelques verres à partager et la participation au quiz.
EOF

            ],
            [
                'code' => 'lyon',
                'meetup_id' => '247452135',
                'content' => <<<EOF
RDV à 19h au Bomp, 1, place Croix-Paquet pour partager quelques verres et participer au quiz. 
EOF
            ],
            [
                'code' => 'montpellier',
                'meetup_id' => '247317894',
                'content' => <<<EOF
RDV à 19h à la Baraka Jeux, allée d'Ulysse, Quartier Odysseum, lieu de rendez-vous bien nommé pour participer au quiz qui mettra en compétition les antennes AFUP en compagnie des développeurs locaux ! 
EOF
            ],
            [
                'code' => 'poitiers',
                'meetup_id' => '247317894',
                'content' => <<<EOF
RDV chez Cobalt, rue victor Hugo, de 18h30 à 21h pour une session de live-coding et la participation au quiz.
La soirée est portée par <a href="https://www.cobaltpoitiers.fr/">Cobalt</a> ainsi qu’<a href="https://www.altern-active.com/">Altern’Active</a>, <a href="https://www.nicolasattard.fr/">NA Corp</a> et <a href="http://www.izi2c.com/">Izi 2C</a>, qu’on remercie de leur soutien.
EOF
            ],
            [
                'code' => 'reims',
                'meetup_id' => '247641676',
                'content' => <<<EOF
de 18h30 à 21h, RDV chez GFI Informatique, 7 rue Pierre Hadot , pour partager quelques verres, participer au quiz, et sans doute écouter quelques talks. Vous pouvez d’ailleurs soumettre un sujet (https://docs.google.com/forms/d/1VlzTbNxxjBJebwP5G8rhUvuboIkpnSZFg9OBBF5JjRA/viewform?edit_requested=true)  si vous le souhaitez ! Merci à GFI Informatique (https://www.gfi.world/fr/ ) pour son soutien. 
EOF
            ],
            [
                'code' => 'rennes',
                'meetup_id' => '247525777',
                'content' => <<<EOF
vous êtes attendus à 19h à la French Tech Rennes, 2 rue de la mabilais, pour 2 talks, un verre de l’amitié, et bien sûr le quiz (qui a d’ailleurs était développé par le coordinateur rennais, merci à lui !) !. La soirée est portée par leur sponsor Digitaleo (https://www.digitaleo.fr/ ), qu’on remercie du soutien.
Infos supplémentaire et inscriptions :
EOF
            ],
            [
                'code' => 'toulouse',
                'content' => <<<EOF
Infos à venir.
EOF
            ],
        ];


        return $this->render(':site:superapero.html.twig', ['aperos' => $aperos]);
    }

    public function voidAction(Request $request)
    {
        $params = [];
        if ($request->attributes->has('legacyContent')) {
            $params = $request->attributes->get('legacyContent');
        }

        return $this->render('site/base.html.twig', $params);
    }
}
