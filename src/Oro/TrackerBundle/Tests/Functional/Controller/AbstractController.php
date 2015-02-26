<?php

namespace Oro\TrackerBundle\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use \Doctrine\Common\DataFixtures\Purger\ORMPurger;
use \Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use \Doctrine\Common\DataFixtures\Loader;

use Oro\TrackerBundle\Tests\Functional\Fixture\LoadDataFixtures;

abstract class AbstractController extends WebTestCase
{
    protected static $loginUsername = LoadDataFixtures::ADMIN_USERNAME;

    protected static $loginPassword = LoadDataFixtures::ADMIN_PASSWORD;

    protected static $client;

    protected static $container;

    protected static $em;

    protected static $fixture;

    public static function setUpBeforeClass()
    {
        self::$client = self::createClient();
        self::$client->followRedirects(true);
        self::$container = self::$client->getKernel()->getContainer();
        self::$em = self::$container->get('doctrine')->getManager();
 
        // Purge tables
        self::$em->getConnection()->executeUpdate("SET foreign_key_checks = 0;");
        $purger = new ORMPurger(self::$em);
        $executor = new ORMExecutor(self::$em, $purger);
        $executor->purge();
        self::$em->getConnection()->executeUpdate("SET foreign_key_checks = 1;");

        // Load fixtures
        $loader = new Loader;
        $fixtures = new LoadDataFixtures();
        $fixtures->setContainer(self::$container);
        $loader->addFixture($fixtures);
        $executor->execute($loader->getFixtures());

        self::authUser();

        self::$fixture = $fixtures;
    }

    /**
     * @return LoadDataFixtures
     */
    public function getFixture()
    {
        return self::$fixture;
    }

    public static function authUser()
    {
        $crawler = self::$client->request('GET', self::getUrl('fos_user_security_login'));

        $form = $crawler->selectButton('_submit')->form();

        $form['_username'] = self::$loginUsername;
        $form['_password'] = self::$loginPassword;

        self::$client->submit($form);
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    public static function getUrl($route, $params = array())
    {
        return self::$container->get('router')->generate($route, $params, false);
    }
}
