<?php

namespace GSB\AppBundle\Tests\Controller;

use Doctrine\ORM\EntityManager;
use GSB\AppBundle\Controller\FichefraisController;
use GSB\AppBundle\Entity\FicheFrais;
use GSB\AccountBundle\Entity\Visiteur;
use GSB\AppBundle\Entity\Etat;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Kernel;



require_once dirname(__DIR__).'/../../../../app/AppKernel.php';

/**
 * Test case class helpful with Entity tests requiring the database interaction.
 * For regular entity tests it's better to extend standard \PHPUnit_Framework_TestCase instead.
 */
class KernelAwareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \AppKernel
     */
    protected $kernel;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @return null
     */
    public function setUp()
    {
        $this->kernel = new \AppKernel('test', true);
        $this->kernel->boot();

        $this->container = $this->kernel->getContainer();
        $this->entityManager = $this->container->get('doctrine')->getManager();

        $this->entityManager->beginTransaction();

        parent::setUp();
    }

    /**
     * @return null
     */
    public function tearDown()
    {
        $this->entityManager->rollback();

        parent::tearDown();
    }



    /**
     * @param $visiteur
     * @param $dateSelectionee
     * @param $em
     */
    public function testAllowModifyFicheFrais()
    {
        $controller = new FicheFraisController();
        /** @var EntityManager $em */
        $ficheFrais = new FicheFrais();
        $etat = new Etat();
        $etat->setId('CL');
        $visiteur = new Visiteur();
        $visiteur->setId('a17');

        $this->entityManager->merge($visiteur);
        $this->entityManager->merge($etat);
        $this->entityManager->flush();

        $ficheFrais->setVisiteur($visiteur);
        $ficheFrais->setMois('208004');
        $ficheFrais->setEtat($etat);

        $this->entityManager->persist($ficheFrais);
        $this->entityManager->flush();

        $return = $controller->allowModifyFicheFrais($ficheFrais->getVisiteur(), $ficheFrais->getMois(), $em);
        $this->entityManager->remove($ficheFrais);
        $this->entityManager->flush();

        if($ficheFrais->getEtat() == 'CL') {

            $this->assertEquals($return, true);
        }

    }
}
