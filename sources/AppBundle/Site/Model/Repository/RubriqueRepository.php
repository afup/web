<?php

namespace AppBundle\Site\Model\Repository;

use AppBundle\Site\Model\Rubrique;
use Afup\Site\Utils\Base_De_Donnees;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Repository\HydratorArray;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use Aura\SqlQuery\Common\SelectInterface;
use Assert\Assertion;
use Exception;

class RubriqueRepository extends Repository implements MetadataInitializer
{
    public function getOneById($id)
    {
        $req = 'SELECT * FROM afup_site_rubrique WHERE id ='. $id . ';';
        return $GLOBALS['AFUP_DB']->obtenirEnregistrement($req, MYSQLI_BOTH);
    }

    public function getAllRubriques($champs = '*', $ordre = 'nom', $direction='desc', $filtre = null, $associatif = false)
    {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_site_rubrique ';
        if (strlen(trim($filtre)) > 0) {
            $requete .= sprintf(' WHERE afup_site_rubrique.nom LIKE %s ', $GLOBALS['AFUP_DB']->echapper('%' . $filtre . '%'));
        }
        $requete .= 'ORDER BY ' . $ordre;
        $query = $this->getQuery($requete);

        $rubriques = [];
        if ($champs === '*') {
            foreach ($query->query($this->getCollection(new HydratorArray()))->getIterator() as $row) {
                $rubriques[] = array(
                    'id'=> $row['id'],
                    'nom' => $row['nom'], 
                    'date' => $row['date'], 
                    'etat' => $row['etat'],
                );
            }
        } else {
            $expected = explode(',',trim($champs));
            foreach ($query->query($this->getCollection(new HydratorArray()))->getIterator() as $row) {
                $data = array();

                foreach ($expected as $key=>$value) {
                    $data[$key] =  $value;
                }
                $rubriques[] = $data;
            }
        }
        return $associatif ? $GLOBALS['AFUP_DB']->obtenirAssociatif($requete) : $GLOBALS['AFUP_DB']->obtenirTous($requete);
    }
    
    public function insertRubrique($rubrique)
    {
        $requete = 'INSERT INTO afup_site_rubrique
        SET
        id_parent            = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->getIdParent()) . ',
        id_personne_physique = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->getIdPersonnePhysique()) . ',
        position             = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->getPosition()) . ',
        date                 = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->getDate()) . ',
        nom                  = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->getNom()) . ',
        raccourci            = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->getRaccourci()) . ',
        descriptif           = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->getDescriptif()) . ',
        contenu              = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->getContenu()) . ',
        icone                = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->getIcone()) . ',
        feuille_associee     = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->getFeuilleAssociee()) . ',
        etat                 = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->getEtat());
        if ($rubrique->getId() > 0) {
            $requete .= ', id            = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->id);
        }
        $query = $this->getQuery($requete);
        $resultat = $query->execute();
        if ($resultat) {
            $this->id = $GLOBALS['AFUP_DB']->obtenirDernierId();
        }
        return $resultat;
    }

    public function updateRubrique ($rubrique) 
    {
        $requete = 
            'UPDATE afup_site_rubrique SET
                id_parent            = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->getIdParent()) . ',
                id_personne_physique = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->getIdPersonnePhysique()) . ',
                position             = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->getPosition()) . ',
                date                 = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->getDate()) . ',
                nom                  = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->getNom()) . ',
                raccourci            = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->getRaccourci()) . ',
                descriptif           = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->getDescriptif()) . ',
                contenu              = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->getContenu()) . ',
                pagination           = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->getPagination()) . ',
                icone                = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->getIcone()) . ',
                etat                 = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->getEtat()) . ',
                feuille_associee     = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->getFeuilleAssociee()) . '
            WHERE id             = ' . $GLOBALS['AFUP_DB']->echapper($rubrique->getId())
        ;
        return $GLOBALS['AFUP_DB']->executer($requete);
    }

    public function deleteRubrique ($id) 
    {
        $requete =  'DELETE FROM afup_site_rubrique WHERE id = '. $id .';' ;
        return $GLOBALS['AFUP_DB']->executer($requete);
    }

    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);
        $metadata->setEntity(Rubrique::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_site_rubrique');
        $metadata
        ->addField([
            'columnName' => 'id',
            'fieldName' => 'id',
            'primary'       => true,
            'autoincrement' => true,
            'type' => 'int',
        ])
        ->addField([
            'columnName' => 'id_parent',
            'fieldName' => 'id_parent',
            'type' => 'int',
        ]) 
        ->addField([
            'columnName' => 'nom',
            'fieldName' => 'nom',
            'type' => 'string',
        ])
        ->addField([
            'columnName' => 'raccourci',
            'fieldName' => 'raccourci',
            'type' => 'string',
        ])
        ->addField([
            'columnName' => 'contenu',
            'fieldName' => 'contenu',
            'type' => 'string',
        ])
        ->addField([
            'columnName' => 'descriptif',
            'fieldName' => 'descriptif',
            'type' => 'string',
        ])
        ->addField([
            'columnName' => 'position',
            'fieldName' => 'position',
            'type' => 'int',
        ])
        ->addField([
            'columnName' => 'date',
            'fieldName' => 'date',
            'type' => 'int',
        ])
        ->addField([
            'columnName' => 'etat',
            'fieldName' => 'etat',
            'type' => 'int',
        ]) 
        ->addField([
            'columnName' => 'id_personne_physique',
            'fieldName' => 'id_personne_physique',
            'type' => 'int',
        ])
        ->addField([
            'columnName' => 'icone',
            'fieldName' => 'icone',
            'type' => 'string',
        ])
        ->addField([
            'columnName' => 'pagination',
            'fieldName' => 'pagination',
            'type' => 'int',
        ])
        ->addField([
            'columnName' => 'feuille_associee',
            'fieldName' => 'feuille_associee',
            'type' => 'int',
        ])
    ;
    return $metadata;
    }
}