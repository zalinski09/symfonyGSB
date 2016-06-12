<?php

namespace GSB\AppBundle\Tests;

use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\DependencyInjection\Container;

require_once dirname(__DIR__).'/../../../app/AppKernel.php';

abstract class TestCase extends \PHPUnit_Framework_TestCase
{

    protected $kernel;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Container
     */
    protected $container;

    public function setUp()
    {
// Boot the AppKernel in the test environment and with the debug.
        $this->kernel = new \AppKernel('test', true);
        $this->kernel->boot();

// Store the container and the entity manager in test case properties
        $this->container = $this->kernel->getContainer();
        $this->entityManager = $this->container->get('doctrine')->getManager('test');

// Build the schema for sqlite
        $this->generateSchema();

        parent::setUp();
    }

    public function tearDown()
    {
// Shutdown the kernel.
        $this->kernel->shutdown();

        parent::tearDown();
    }

    protected function generateSchema()
    {
// Get the metadata of the application to create the schema.
        $metadata = $this->getMetadata();

        if ( ! empty($metadata)) {
// Create SchemaTool
            $tool = new SchemaTool($this->entityManager);
            $tool->createSchema($metadata);
        } else {
            throw new SchemaException('No Metadata Classes to process.');
        }
    }

    /**
     * Overwrite this method to get specific metadata.
     *
     * @return Array
     */
    protected function getMetadata()
    {
        $realEntityManager = $this->container->get('doctrine')->getManager('default');
        return $realEntityManager->getMetadataFactory()->getAllMetadata();
    }
}