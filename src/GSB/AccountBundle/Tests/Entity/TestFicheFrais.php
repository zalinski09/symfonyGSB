<?php
/**
 * 
 * User: Julien
 * Date: 17/05/2016
 * Time: 17:26
 */
namespace GSB\AccountBundle\Tests\Entity;

use GSB\AppBundle\Entity\FicheFrais;
use GSB\AppBundle\Entity\Etat;
use GSB\AppBundle\Tests\TestCase;


require_once dirname(__DIR__).'/../../AppBundle/Tests/TestCase.php';

class TestFicheFrais extends TestCase  {

    public function testGenerateLocalization()
    {
        /** @var Etat $etat */
        $etat = new Etat();
        $etat->setId('CL');
        $etat->setLibelle('Saisie Cloturée');

        // Save the etat
        $this->entityManager->persist($etat);
        $this->entityManager->flush();
    }


} 