<?php
namespace Oro\TrackerBundle\Tests\Functional\Controller;

use Oro\TrackerBundle\Tests\Functional\Fixture\LoadDataFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\Tools\SchemaTool;

abstract class AbstractController extends WebTestCase
{
    protected $client;

    protected $container;

    protected $em;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $this->container = static::$kernel->getContainer();
        $this->em = $this->container->get('doctrine')->getManager();
        $classes = $this->em->getMetadataFactory()->getAllMetadata();
        $tool = new SchemaTool($this->em);
        $tool->dropSchema($classes);
        $tool->createSchema($classes);
        (new LoadDataFixtures())->setContainer($this->container)->load($this->em);

        $this->client = $this->createClient();
    }

    public function getUrl($route, $params = array())
    {
        $this->container->get('router')->generate($route, $params, false);
    }
}
