<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Testresult;
use Tests\AppBundle\TestHelpers;
use Doctrine\ORM\EntityManager;
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

        echo $client->getResponse()->getContent();

        $link = $crawler->filter('a:contains("＋ Add another testcase")')->eq(0)->link();
        $crawler = $client->click($link);

        $buttonNode = $crawler->selectButton('Start monitoring');
        $form = $buttonNode->form();

        $client->submit($form, array(
            'testcase[title]' => 'Blafasel',
            'testcase[cadence]' => '*/5',
            'testcase[script]' => 'bar',
        ));

        $crawler = $client->followRedirect();

        $this->assertSame('The new testcase has been added.', trim($crawler->filter('aside.messages section.container div.alert.alert-success')->first()->text()));

        $this->assertSame(1, count($crawler->filter('h4 a:contains("Blafasel")')));
    }

    public function testThatEnabledTestcasesAreListedFirst()
    {
        $this->resetDatabase();
        $client = $this->createAndActivateDemoUser(); // This gives us the first testcase

        $crawler = $client->request('GET', '/testcases/');

        $link = $crawler->filter('a:contains("＋ Add another testcase")')->eq(0)->link();
        $crawler = $client->click($link);

        $buttonNode = $crawler->selectButton('Start monitoring');
        $form = $buttonNode->form();

        $client->submit($form, array(
            'testcase[title]' => 'Second, disabled Testcase',
            'testcase[cadence]' => '*/5',
            'testcase[script]' => 'bar',
        ));

        $crawler = $client->followRedirect();

        $link = $crawler->filter('a:contains("＋ Add another testcase")')->eq(0)->link();
        $crawler = $client->click($link);

        $buttonNode = $crawler->selectButton('Start monitoring');
        $form = $buttonNode->form();

        $client->submit($form, array(
            'testcase[title]' => 'Third, enabled Testcase',
            'testcase[cadence]' => '*/5',
            'testcase[script]' => 'bar',
        ));

        $crawler = $client->followRedirect();

        // Disable the second testcase
        $buttonNode = $crawler->selectButton('Disable')->eq(1);
        $form = $buttonNode->form();
        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertSame('Demo User Testcase One', trim($crawler->filter('.testcase-entry-cell h4')->eq(0)->text()));
        $this->assertSame('Third, enabled Testcase', trim($crawler->filter('.testcase-entry-cell h4')->eq(1)->text()));
        $this->assertSame('Second, disabled Testcase', trim($crawler->filter('.testcase-entry-cell h4')->eq(2)->text()));
    }

    public function testIndexWithTestcaseWithoutResults()
    {
        $this->resetDatabase();
        $client = $this->createAndActivateDemoUser();

        $crawler = $client->request('GET', '/testcases/');

        echo $client->getResponse()->getContent();

        $this->assertSame(1, count($crawler->filter('h4 a')));

        $this->assertSame(1, count($crawler->filter('h4 a:contains("Demo User Testcase One")')));

        $this->assertSame(
            1,
            count($crawler->filter('table.testcases small:contains("No test run results yet.")'))
        );

        $this->assertSame(
            1,
            count($crawler->filter('table.testcases span.label-success:contains("Enabled")'))
        );

        $this->assertSame(
            1,
            count($crawler->filter('table.testcases span.label-default:contains("*/5")'))
        );
    }

    public function testIndexWithTestcaseWithResults()
    {
        $this->resetDatabase();
        $client = $this->createAndActivateDemoUser();

        $container = $client->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $tcRepo = $em->getRepository('AppBundle\Entity\Testcase');

        $testresult = new Testresult();
        $testresult->setId('abc');
        $datetimeRun = new \DateTime('now');
        $testresult->setDatetimeRun($datetimeRun);
        $testresult->setExitCode(0);
        $testresult->setTestcase($tcRepo->findOneBy(['title' => 'Demo User Testcase One']));
        $testresult->setOutput('foo');
        $testresult->setHar('bar');
        $testresult->setFailScreenshotFilename('baz');
        $em->persist($testresult);
        $em->flush();

        $crawler = $client->request('GET', '/testcases/');

        $this->assertSame(
            0,
            count($crawler->filter('table.testcases small:contains("No test run results yet.")'))
        );

        //┌ 2015-06-01 at 14:35
        $this->assertContains(
            '┌',
            $crawler->filter('.testcase-entry-row')->first()->text()
        );

        $this->assertContains(
            'today at ' . $datetimeRun->format('H:i'),
            $crawler->filter('.testcase-entry-row')->first()->text()
        );
    }

    public function testIndexDisableAndEnableTestcase()
    {
        $this->resetDatabase();
        $client = $this->createAndActivateDemoUser();
        $crawler = $client->request('GET', '/testcases/');

        $buttonNode = $crawler->selectButton('Disable');
        $form = $buttonNode->form();
        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertSame('Disabled', trim($crawler->filter('td.testcase-entry-cell span.label')->eq(1)->text()));

        $buttonNode = $crawler->selectButton('Enable');
        $form = $buttonNode->form();
        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertSame('Enabled', trim($crawler->filter('td.testcase-entry-cell span.label')->eq(1)->text()));
    }

    public function testEdit()
    {
        $this->resetDatabase();
        $client = $this->createAndActivateDemoUser();

        $crawler = $client->request('GET', '/testcases/');

        $link = $crawler->filter('a:contains("Edit")')->first()->link();

        $crawler = $client->click($link);

        $buttonNode = $crawler->selectButton('Update testcase');
        $form = $buttonNode->form();

        $crawler = $client->submit($form, array(
            'testcase[title]' => 'Updated testcase',
            'testcase[cadence]' => '*/15',
            'testcase[script]' => 'foo',
        ));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSame(
            'The testcase has been updated.',
            trim($crawler->filter('div.alert.alert-success')->first()->text())
        );

        $link = $crawler->filter('a:contains("◀ Back to testcases list")')->first()->link();

        $crawler = $client->click($link);

        $this->assertSame(1, count($crawler->filter('h4 a:contains("Updated testcase")')));

        $this->assertSame(
            1,
            count($crawler->filter('table.testcases span.label-default:contains("*/15")'))
        );
    }
}
