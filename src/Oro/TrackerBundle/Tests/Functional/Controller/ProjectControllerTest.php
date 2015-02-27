<?php

namespace Oro\TrackerBundle\Tests\Functional\Controller;

class ProjectControllerTest extends AbstractController
{
    public function testAddProject()
    {
        $crawler = self::$client->request('GET', $this->getUrl('_tracking_project_edit'));

        $form = $crawler->selectButton('tracker_project[Save]')->form();

        $form['tracker_project[label]'] = 'label';
        $form['tracker_project[summary]'] = 'summary';
        $form['tracker_project[code]'] = 'first';

        $crawler = self::$client->submit($form);

        $this->assertTrue(self::$client->getResponse()->isSuccessful());

        $this->assertEquals(
            1,
            $crawler->filter('html:contains("Project has been added")')->count()
        );
    }

    public function testEditProject()
    {
        $crawler = self::$client->request('GET', $this->getUrl('_tracking_project_list'));

        $link = $crawler->filter('a:contains("Edit")')->link();
        $crawler = self::$client->click($link);

        $form = $crawler->selectButton('tracker_project[Save]')->form();

        $form['tracker_project[label]'] = 'label';
        $form['tracker_project[summary]'] = 'summary';
        $form['tracker_project[code]'] = 'first_changed';

        $crawler = self::$client->submit($form);

        $this->assertTrue(self::$client->getResponse()->isSuccessful());

        $this->assertEquals(
            1,
            $crawler->filter('html:contains("Project has been updated")')->count()
        );
    }

    public function testShowProject()
    {
        $crawler = self::$client->request(
            'GET',
            $this->getUrl(
                '_tracking_project_show',
                array('projectCode' => 'FIRST_CHANGED')
            )
        );

        $this->assertEquals(
            1,
            $crawler->filter('div.breadcrumb:contains("FIRST_CHANGED")')->count()
        );
    }
}
