<?php

namespace Tests\AppBundle\Command;

use AppBundle\Command\ImportStatisticsCommand;
use AppBundle\Entity\Testcase;
use AppBundle\Entity\Testresult;
use AppBundle\Entity\User;
use Symfony\Component\DependencyInjection\Tests\Compiler\CheckExceptionOnInvalidReferenceBehaviorPassTest;
use Tests\AppBundle\TestHelpers;
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
        /** @var Testcase $testcase */
        $testcase = $testcaseRepo->findOneBy(['title' => 'Demo User Testcase One']);
        $testcase->setId('tc1');
        $testcase->setCreatedAt(new \DateTime('2015-01-10 12:34:56', new \DateTimeZone('Europe/Berlin')));
        $em->persist($testcase);

        /** @var Testcase $testcase */
        $user = $testcase->getUser();

        $testresult = new Testresult();
        $testresult->setTestcase($testcase);
        $testresult->setId('tr1');
        $testresult->setDatetimeRun(new \DateTime('2015-06-11 12:34:56', new \DateTimeZone('Europe/Berlin')));
        $testresult->setExitCode(0);
        $testresult->setOutput('');
        $em->persist($testresult);

        $em->flush();


        $testcase = new Testcase();
        $testcase->setUser($user);
        $testcase->setTitle('Demo User Testcase Two');
        $testcase->setScript('foo');
        $testcase->setCadence('*/5');
        $testcase->setEnabled(true);
        $em->persist($testcase);
        // need to overwrite those after first persist where they get autoset
        $testcase->setId('tc2');
        $testcase->setCreatedAt(new \DateTime('2015-02-10 12:34:56', new \DateTimeZone('Europe/Berlin')));
        $em->persist($testcase);

        $testresult = new Testresult();
        $testresult->setTestcase($testcase);
        $testresult->setId('tr3');
        $testresult->setDatetimeRun(new \DateTime('2015-06-13 12:34:56', new \DateTimeZone('Europe/Berlin')));
        $testresult->setExitCode(0);
        $testresult->setOutput('');
        $em->persist($testresult);

        $em->flush();


        $mock = new Mock([
            file_get_contents(__DIR__ . '/../fixtures/statistics.testcase1.httpresponse'),
            file_get_contents(__DIR__ . '/../fixtures/statistics.testcase2.httpresponse')
        ]);
        $guzzleClient = $container->get('guzzle_client');
        $guzzleClient->getEmitter()->attach($mock);

        $command = $application->find('journeymonitor:control:import:statistics');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'url' => 'http://foo.bar/testcases/:testcaseId/statistics/latest/?minTestresultDatetimeRun=:minTestresultDatetimeRun'
        ]);


        $statisticsRepo = $em->getRepository('AppBundle\Entity\Statistics');
        $statistics = $statisticsRepo->find('tr1');

        $this->assertSame(1234, $statistics->getRuntimeMilliseconds());
        $this->assertSame(100, $statistics->getNumberOf200());
        $this->assertSame(110, $statistics->getNumberOf400());
        $this->assertSame(11, $statistics->getNumberOf500());

        $statistics = $statisticsRepo->find('tr3');

        $this->assertSame(3234, $statistics->getRuntimeMilliseconds());
        $this->assertSame(300, $statistics->getNumberOf200());
        $this->assertSame(130, $statistics->getNumberOf400());
        $this->assertSame(33, $statistics->getNumberOf500());

        $expected = <<<EOT
Consuming URL http://foo.bar/testcases/tc1/statistics/latest/?minTestresultDatetimeRun=2015-01-10+12%3A34%3A10%2B0100.
Imported statistics for testresult tr1.
Could not persist statistics for testresult tr2 because the testresult does not exist.
Consuming URL http://foo.bar/testcases/tc2/statistics/latest/?minTestresultDatetimeRun=2015-02-10+12%3A34%3A10%2B0100.
Imported statistics for testresult tr3.
Could not persist statistics for testresult tr4 because the testresult does not exist.
Import finished.

EOT;

        $this->assertSame($expected, $commandTester->getDisplay());
    }
}
