<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\TestHelpers;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestcasesControllerWebTest extends WebTestCase
{
    use TestHelpers;

    public function testIndex()
    {
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
        $this->resetDatabase();

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

        $container = $client->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');

        $repo = $em->getRepository('AppBundle\Entity\Testcase');

        $testcase = $repo->findOneBy(['title' => 'Testcase One']);
        $this->assertSame('bar', $testcase->getScript());

        $this->assertSame('manuel@kiessling.net', $testcase->getUser()->getEmail());
    }

    public function testDemoMode() {
        $this->resetDatabase();
        $this->createDemoUser();

        $client = static::createClient();
        $crawler = $client->request('GET', '/demo/testcases/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals('You are currently in demo mode.', trim($crawler->filter('div.container div.alert.alert-info')->first()->text()));
        $this->assertEquals('Demo User Testcase One', trim($crawler->filter('h4')->eq(1)->text()));
        $this->assertEquals('Not available in demo mode', trim($crawler->filter('div.row div.col-lg-12 a.pull-right')->first()->attr('title')));
    }

    public function testNotDemoMode() {
        $this->resetDatabase();
        $this->createDemoUser();

        $client = static::createClient();
        $crawler = $client->request('GET', '/testcases/');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}
