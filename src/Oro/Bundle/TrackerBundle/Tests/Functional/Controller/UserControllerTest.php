<?php

namespace Oro\Bundle\TrackerBundle\Tests\Functional\Controller;

class UserControllerTest extends AbstractController
{
    public function testAdd()
    {
        $crawler = self::$client->request('GET', $this->getUrl('fos_user_registration_register'));

        $form = $crawler->filter('.fos_user_registration_register')->form();

        $form['fos_user_registration_form[email]'] = 'qq@qq.rr';
        $form['fos_user_registration_form[username]'] = 'unique321';
        $form['fos_user_registration_form[plainPassword][first]'] = '111';
        $form['fos_user_registration_form[plainPassword][second]'] = '111';
        $form['fos_user_registration_form[fullname]'] = 'Full Name';
        $form['fos_user_registration_form[timezone]'] = 'America/Los_Angeles';
        $form['fos_user_registration_form[roles]'] = 'ROLE_MANAGER';

        $crawler = self::$client->submit($form);

        $this->assertTrue(self::$client->getResponse()->isSuccessful());

        $this->assertEquals(
            1,
            $crawler->filter('html:contains("User has been added")')->count()
        );
    }

    public function testEdit()
    {
        $crawler = self::$client->request(
            'GET',
            $this->getUrl(
                '_tracking_user_edit',
                array(
                    'id' => self::getFixture()->getAdminUser()->getId())
            )
        );

        $form = $crawler->selectButton('save')->form();

        $form['fos_user_registration_form[fullname]'] = 'Admin number one';

        $crawler = self::$client->submit($form);

        $this->assertTrue(self::$client->getResponse()->isSuccessful());

        $this->assertEquals(
            1,
            $crawler->filter('html:contains("User has been updated")')->count()
        );
    }

    public function testShowUserProfile()
    {
        $crawler = self::$client->request(
            'GET',
            $this->getUrl(
                '_tracking_user_profile',
                array('username' => self::getFixture()->getAdminUser()->getUsername())
            )
        );

        $this->assertTrue(self::$client->getResponse()->isSuccessful());

        $this->assertEquals(
            1,
            $crawler->filter('html:contains("User name: '.self::getFixture()->getAdminUser()->getUsername().'")')
                ->count()
        );
    }
}
