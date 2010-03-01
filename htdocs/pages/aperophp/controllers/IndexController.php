<?php
/**
 * Controller Principal site 'AperoPHP'
 * 
 * @author    Perrick Penet   <perrick@noparking.fr>
 * @author    Olivier Hoareau <olivier@phppro.fr>
 * @copyright 2010 Association Française des Utilisateurs de PHP
 * 
 * @category AperoPHP
 * @package  AperoPHP
 * @group    Controllers
 */

require_once 'afup/AFUP_Aperos.php';
require_once 'afup/AFUP_Aperos_Inscrits.php';

class IndexController extends Zend_Controller_Action
{
    /**
     * url: /
     */
    public function indexAction()
    {
    	$aperos           = array();
    	$tableDesAperos   = new AFUP_Aperos();
    	$tableDesInscrits = new AFUP_Aperos_Inscrits();
    	
        // derniers apéros à afficher en page d'accueil
        
        $derniersAperos = $tableDesAperos->fetchAll(null, 'date DESC', 10);
        
        if (!empty($derniersAperos)) {
            foreach ($derniersAperos as $apero) {
                $aperos[$apero->id]['apero'] = $apero;
                $aperos[$apero->id]['responsable'] = $tableDesInscrits->find($apero->idResponsable);    	
            }    	
        }
        
        Zend::registry('view')->aperos = $aperos;       
                
        echo Zend::registry('view')->render('index.php');        
    }
}