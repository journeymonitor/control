<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseTestCase;

class TestcasesControllerTest extends BaseTestCase
{
    public function testIndex()
    {
        $this->resetDatabase();

        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testIndexContainsPasswordField()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertTrue($crawler->filter('#testcase_and_user_user_password')->attr('type') == 'password');
    }

    public function testAddingTestcaseWithNewUserHappyPath()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $buttonNode = $crawler->selectButton('Start monitoring');

        $form = $buttonNode->form();

        $crawler = $client->submit($form, array(
            'testcase_and_user[user][email]' => 'manuel@kiessling.net',
            'testcase_and_user[user][password]' => 'foo',
            'testcase_and_user[testcase][title]' => 'Testcase One',
            'testcase_and_user[testcase][cadence]' => '*/5',
            'testcase_and_user[testcase][script]' => 'bar',
        ));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSame(trim($crawler->filter('div.alert.alert-success')->first()->text()), 'Thank you. Please check your mails in order to activate your account.');
        $this->assertSame(trim($crawler->filter('div.alert.alert-success')->eq(1)->text()), 'The new testcase has been added.');
    }
}
