<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Testresult;
use AppBundle\Entity\Testcase;
use AppBundle\Entity\User;
use AppBundle\Tests\TestHelpers;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestresultsApiControllerWebTest extends WebTestCase
{
    use TestHelpers;

    public function testIndex()
    {
        $this->resetDatabase();

        $client = static::createClient();

        $container = $client->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');

        $user = new User();
        $user->setEmail('user@example.org');
        $user->setUsername($user->getEmail());
        $user->setPassword('abc');
        $user->setSalt('xyz');
        $user->setActivatedAt(new \DateTime());
        $em->persist($user);

        $testcase = new Testcase();
        $testcase->setTitle('Test One');
        $testcase->setUser($user);
        $testcase->setCadence('*/15');
        $testcase->setEnabled(true);
        $testcase->setScript('foo');
        $em->persist($testcase);

        $testresult = new Testresult();
        $testresult->setId('12345');
        $testresult->setTestcase($testcase);
        $testresult->setOutput(file_get_contents(__DIR__ . '/../fixtures/selenese-runner.log'));
        $testresult->setExitCode(0);
        $testresult->setDatetimeRun(new \DateTime());
        $testresult->setHar(file_get_contents(__DIR__ . '/../fixtures/testrun.actual.har.json'));
        $em->persist($testresult);

        $em->flush();

        $client->request('GET', '/api/testresults/' . $testresult->getId() . '/har.jsonp');

        $content = $client->getResponse()->getContent();

        $this->assertSame('/**/onInputData(' .
            json_encode(
                json_decode(
                    file_get_contents(__DIR__ . '/../fixtures/testrun.expected.har.json')
                )
            ) .
            ');', $content);
    }
}
