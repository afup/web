<?php

namespace AppBundle\Controller\Admin\Site;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Site\Model\Repository\RubriqueRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Exception;


class DeleteRubriqueAction
{
    use DbLoggerTrait;

    /** @var FlashBagInterface */
    private $flashBag;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var Environment */
    private $twig;

    /** @var RubriqueRepository */
    private $rubriqueRepository;
    
    public function __construct(RubriqueRepository $rubriqueRepository, UrlGeneratorInterface $urlGenerator,FlashBagInterface $flashBag)
    {
        $this->rubriqueRepository =  $rubriqueRepository;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
    }

    public function __invoke(Request $request)
    {
        $id = $GLOBALS['AFUP_DB']->echapper($request->get('id'));
        $rubrique = $this->rubriqueRepository->getOneById($id);
        $name = $rubrique["nom"];
        try {
            $this->rubriqueRepository->deleteRubrique($id);
            $this->log('Suppression de la Rubrique ' . $name);
            $this->flashBag->add('notice', 'La rubrique '.$name.' a été supprimée');
            return new RedirectResponse($this->urlGenerator->generate('admin_site_rubriques_list'));
        } catch (Exception $e) {
            $this->flashBag->add('error', 'Une erreur est survenue lors de la suppression de la rubrique'. $name);
            return new RedirectResponse($this->urlGenerator->generate('admin_site_rubriques_list'));
        }
    }
}
