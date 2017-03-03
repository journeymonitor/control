<?php

namespace Tests\AppBundle\Functional;

use AppBundle\Entity\Testresult;
use Tests\AppBundle\TestHelpers;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestresultPageFunctionalWebTest extends WebTestCase
{
    use TestHelpers;

    public function test()
    {
        $this->resetDatabase();

        $client = $this->getClientThatRegisteredAndActivatedADemoUser();

        $container = $client->getContainer();

        $em = $container->get('doctrine.orm.entity_manager');
        $tcRepo = $em->getRepository('AppBundle\Entity\Testcase');
        $testcase = $tcRepo->findOneBy(['title' => 'Demo User Testcase One']);

        $testresult = new Testresult();
        $testresult->setId('abc');
        $datetimeRun = new \DateTime('now');
        $testresult->setDatetimeRun($datetimeRun);
        $testresult->setExitCode(0);
        $testresult->setTestcase($testcase);
        $testresult->setOutput('foo');
        $testresult->setHar('bar');
        $testresult->setFailScreenshotFilename('baz');
        $em->persist($testresult);
        $em->flush();

        $crawler = $client->request('GET', '/testresults/' . $testresult->getId());

        $this->assertSame(1, count($crawler->filter('h4:contains("Testresult details")')));
        $this->assertSame(
            '/testcases/' . $testcase->getId(),
            $crawler->filter('a:contains("Demo User Testcase One")')->first()->attr('href')
        );
    }

}
