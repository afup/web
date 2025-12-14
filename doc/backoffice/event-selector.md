# Sélecteur d'évènement

Les pages de gestion des events ont (presque) toutes un sélecteur d'event en haut qui permet de switcher le contexte de
la page en cours d'un event à un autre.

Ce sélecteur ajouter un paramètre `id` dans la query string de l'url et stocke cet id en session, qui est ensuite lu
quand on navigue dans les pages de la section event. Cela permet de ne pas avoir à re-selectionner un event à chaque fois.

## Utilisation

Pour que ce sélecteur fonctionne, il faut plusieurs éléments dans un controller du backoffice :

```php
use AppBundle\Event\AdminEventSelection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class ExampleAction extends AbstractController
{
    public function __invoke(AdminEventSelection $eventSelection): Response
    {
        // Accès direct à l'event sélectionné
        $event = $eventSelection->event;
        
        // ... logique du controller ...
        
        return $this->render('admin/event/rooms.html.twig', [
            // Permet l'affichage du sélecteur
            'event_select_form' => $eventSelection->selectForm(),
        ]);
    }
}
```
Et dans le template :

```html
{% extends 'admin/base_with_header.html.twig' %}

{% block content %}
    <h2>Ma super page</h2>

    {% include 'admin/event/change_event.html.twig' with {form: event_select_form} only %}

    <div>
        Le contenu de la page
    </div>

{% endblock %}
```

## Comment ça fonctionne ?

La classe `AdminEventSelection` est injectée automatiquement grâce au [value resolver][value-resolver] `AppBundle\Controller\ValueResolver\AdminEventSelectionValueResolver`.

[value-resolver]: https://symfony.com/doc/current/controller/value_resolver.html#adding-a-custom-value-resolver
