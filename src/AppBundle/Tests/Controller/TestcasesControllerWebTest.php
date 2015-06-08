<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\TestHelpers;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestcasesControllerWebTest extends WebTestCase
{
    use TestHelpers;

    public function testAddingTestcaseWithNewUserHappyPath()
    {
        $this->resetDatabase();

        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $buttonNode = $crawler->selectButton('Start monitoring');

        $form = $buttonNode->form();

        $client->enableProfiler();

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

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertEquals(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        $this->assertSame('Welcome to JourneyMonitor!', $message->getSubject());
        $this->assertEquals('manuel@kiessling.net', key($message->getTo()));

        $expectedBody = 'Hello,

In order to activate your account and enable your monitoring,
please visit http://localhost/register/confirm/' . $testcase->getUser()->getConfirmationToken() . '

Regards,

--
  The JourneyMonitor Team';

        $this->assertEquals($expectedBody, $message->getBody());
    }

    public function testAddingTestcaseWithExistingUserHappyPath()
    {
        $this->resetDatabase();
        $client = $this->createAndActivateDemoUser();

        $crawler = $client->request('GET', '/testcases/');

        $link = $crawler->filter('a:contains("＋ Add another testcase")')->eq(0)->link();
        $crawler = $client->click($link);

        $buttonNode = $crawler->selectButton('Start monitoring');
        $form = $buttonNode->form();

        $crawler = $client->submit($form, array(
            'testcase[title]' => 'Blafasel',
            'testcase[cadence]' => '*/5',
            'testcase[script]' => 'bar',
        ));

        $this->assertSame('The new testcase has been added.', trim($crawler->filter('div.messages div.alert.alert-success')->first()->text()));

        $link = $crawler->filter('a:contains("◀ Back to testcases list")')->eq(0)->link();
        $crawler = $client->click($link);

        $this->assertSame(1, count($crawler->filter('h4 a:contains("Blafasel")')));
    }
}
