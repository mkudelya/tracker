<?php

namespace Oro\TrackerBundle\Tests\Functional\Controller;

class IssueControllerTest extends AbstractController
{
    public function testAddIssue()
    {
        $crawler = self::$client->request('GET', $this->getUrl('_tracking_project_edit'));

        $form = $crawler->selectButton('tracker_project[Save]')->form();

        $form['tracker_project[label]'] = 'label';
        $form['tracker_project[summary]'] = 'summary';
        $form['tracker_project[code]'] = 'second';

        $crawler = self::$client->submit($form);

        $this->assertTrue(self::$client->getResponse()->isSuccessful());

        $this->assertEquals(
            1,
            $crawler->filter('html:contains("Project has been added")')->count()
        );

        $addIssueLink = $crawler->filter('a:contains("Add issue")')->link();
        $crawler = self::$client->click($addIssueLink);

        $form = $crawler->selectButton('tracker_issue[Save]')->form();

        $form['tracker_issue[summary]'] = 'summary';
        $form['tracker_issue[code]'] = 'story1';
        $form['tracker_issue[description]'] = 'description';
        $form['tracker_issue[priority]'] = '1';
        $form['tracker_issue[type]'] = 'story';

        $crawler = self::$client->submit($form);

        $this->assertTrue(self::$client->getResponse()->isSuccessful());

        $this->assertEquals(
            1,
            $crawler->filter('html:contains("Issue has been added")')->count()
        );
    }

    public function testAddSubIssue()
    {
        $crawler = self::$client->request(
            'GET',
            $this->getUrl(
                '_tracking_issue_show',
                array('projectCode' => 'SECOND', 'issueCode' => 'STORY1')
            )
        );

        $addIssueLink = $crawler->filter('a:contains("Add sub-issue")')->link();
        $crawler = self::$client->click($addIssueLink);

        $form = $crawler->selectButton('tracker_issue[Save]')->form();

        $form['tracker_issue[summary]'] = 'summary';
        $form['tracker_issue[code]'] = 'subissue1';
        $form['tracker_issue[description]'] = 'description';
        $form['tracker_issue[priority]'] = '1';

        $crawler = self::$client->submit($form);

        $this->assertTrue(self::$client->getResponse()->isSuccessful());

        $this->assertEquals(
            1,
            $crawler->filter('html:contains("Issue has been added")')->count()
        );
    }

    public function testEditIssue()
    {
        $crawler = self::$client->request(
            'GET',
            $this->getUrl(
                '_tracking_issue_edit',
                array('projectCode' => 'SECOND', 'issueCode' => 'STORY1')
            )
        );

        $form = $crawler->selectButton('tracker_issue[Save]')->form();

        $form['tracker_issue[summary]'] = 'summary';
        $form['tracker_issue[code]'] = 'story1';
        $form['tracker_issue[description]'] = 'description';
        $form['tracker_issue[priority]'] = '1';

        $crawler = self::$client->submit($form);

        $this->assertTrue(self::$client->getResponse()->isSuccessful());

        $this->assertEquals(
            1,
            $crawler->filter('html:contains("Issue has been updated")')->count()
        );
    }

    public function testShowIssue()
    {
        $crawler = self::$client->request(
            'GET',
            $this->getUrl(
                '_tracking_issue_show',
                array('projectCode' => 'SECOND', 'issueCode' => 'STORY1')
            )
        );

        $this->assertEquals(
            1,
            $crawler->filter('div.breadcrumb:contains("STORY1")')->count()
        );
    }

    public function testAddComment()
    {
        $crawler = self::$client->request(
            'GET',
            $this->getUrl(
                '_tracking_issue_show',
                array('projectCode' => 'SECOND', 'issueCode' => 'STORY1')
            )
        );

        $form = $crawler->selectButton('tracker_comment[Save]')->form();

        $form['tracker_comment[body]'] = 'first comment';

        $crawler = self::$client->submit($form);

        $this->assertTrue(self::$client->getResponse()->isSuccessful());

        $this->assertEquals(
            1,
            $crawler->filter('html:contains("Comment has been added")')->count()
        );
    }

    public function testUpdateComment()
    {
        $crawler = self::$client->request(
            'GET',
            $this->getUrl(
                '_tracking_issue_show',
                array('projectCode' => 'SECOND', 'issueCode' => 'STORY1')
            )
        );

        $form = $crawler->selectButton('Save')->form();

        $form['tracker_comment[body]'] = 'first comment';

        $crawler = self::$client->submit($form);

        $this->assertTrue(self::$client->getResponse()->isSuccessful());

        $this->assertEquals(
            1,
            $crawler->filter('html:contains("Comment has been updated")')->count()
        );
    }

    public function testRemoveComment()
    {
        $crawler = self::$client->request(
            'GET',
            $this->getUrl(
                '_tracking_issue_show',
                array('projectCode' => 'SECOND', 'issueCode' => 'STORY1')
            )
        );

        $addIssueLink = $crawler->filter('div#body a:contains("Remove")')->link();
        $crawler = self::$client->click($addIssueLink);

        $this->assertTrue(self::$client->getResponse()->isSuccessful());

        $this->assertEquals(
            1,
            $crawler->filter('html:contains("Comment has been deleted")')->count()
        );
    }
}
