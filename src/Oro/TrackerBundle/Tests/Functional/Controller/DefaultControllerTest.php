<?php
namespace Oro\TrackerBundle\Tests\Functional\Controller;

class DefaultControllerTest extends AbstractController
{
    public function testIndexAction()
    {
        $crawler = $this->client->request('GET', $this->getUrl('tracker_homepage'));

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("abc")')->count()
        );
    }
}
