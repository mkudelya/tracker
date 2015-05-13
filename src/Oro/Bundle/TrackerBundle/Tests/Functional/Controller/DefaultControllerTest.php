<?php

namespace Oro\Bundle\TrackerBundle\Tests\Functional\Controller;

class DefaultControllerTest extends AbstractController
{
    public function testIndexAction()
    {
        $crawler = self::$client->request('GET', self::getUrl('tracker_homepage'));

        $this->assertTrue(self::$client->getResponse()->isSuccessful());

        $this->assertEquals(
            1,
            $crawler->filter('html:contains("Logout")')->count()
        );
    }
}
