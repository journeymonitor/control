<?php

namespace AppBundle\Tests\Command;

use AppBundle\Command\ImportStatisticsCommand;
use AppBundle\Entity\Testresult;
use AppBundle\Tests\TestHelpers;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use GuzzleHttp\Subscriber\Mock;

// Building upon WebTestCase because this gives us a user with a testcase for free
class ImportStatisticsCommandTest extends WebTestCase
{
    use TestHelpers;

    public function testExecute()
    {
        $this->resetDatabase();
        $client = $this->createAndActivateDemoUser();

        $kernel = $client->getKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new ImportStatisticsCommand());

        $container = $kernel->getContainer();
        $em = $container->get('doctrine')->getManager();

        $testcaseRepo = $em->getRepository('AppBundle\Entity\Testcase');
        $testcase = $testcaseRepo->findOneBy(['title' => 'Demo User Testcase One']);
        $testcase->setId('tc1');
        $em->persist($testcase);
        $em->flush();

        $testresult = new Testresult();
        $testresult->setTestcase($testcase);
        $testresult->setId('tr1');
        $testresult->setDatetimeRun(new \DateTime('2015-06-12 12:34:56'));
        $testresult->setExitCode(0);
        $testresult->setOutput('');
        $em->persist($testresult);
        $em->flush();

        $mock = new Mock([
            file_get_contents(__DIR__ . '/../fixtures/statistics.httpresponse')
        ]);
        $guzzleClient = $container->get('guzzle_client');
        $guzzleClient->getEmitter()->attach($mock);

        $command = $application->find('journeymonitor:control:import:statistics');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'url' => 'http://foo.bar/'
        ]);

        $statisticRepo = $em->getRepository('AppBundle\Entity\Statistic');
        $statistic = $statisticRepo->find('tr1');

        $this->assertSame(1234, $statistic->getRuntimeMilliseconds());
        $this->assertSame(10, $statistic->getNumberOf200());
        $this->assertSame(2, $statistic->getNumberOf400());
        $this->assertSame(1, $statistic->getNumberOf500());

        $this->assertSame("Imported statistics for testresult tr1.\nCould not persist statistics for testresult tr2 because the testresult does not exist.\nImport finished.\n", $commandTester->getDisplay());
    }
}
