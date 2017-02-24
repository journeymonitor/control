<?php

namespace Tests\AppBundle\Repository;

use AppBundle\Entity\Statistics;
use AppBundle\Entity\Testcase;
use AppBundle\Entity\Testresult;
use AppBundle\Repository\TestresultRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\TestHelpers;

class TestresultRepositoryTest extends WebTestCase
{
    use TestHelpers;

    public function testGetLatestByTestcaseWithStatisticsIterator()
    {
        $this->resetDatabase();
        $client = $this->getClientThatRegisteredAndActivatedADemoUser();

        $kernel = $client->getKernel();
        $kernel->boot();

        $container = $kernel->getContainer();

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $container->get('doctrine')->getManager();

        $testcaseRepo = $em->getRepository('AppBundle\Entity\Testcase');
        /** @var Testcase $testcase */
        $testcase = $testcaseRepo->findOneBy(['title' => 'Demo User Testcase One']);

        /** @var Testresult $testresult */
        $testresult = new Testresult();
        $testresult->setId('abc');
        $testresult->setExitCode(0);
        $testresult->setDatetimeRun(new \DateTime('now', new \DateTimeZone('Europe/Berlin')));
        $testresult->setOutput('');
        $testresult->setTestcase($testcase);
        $em->persist($testresult);
        $em->flush();

        /** @var Statistics $statistics */
        $statistics = new Statistics();
        $statistics->setTestresult($testresult);
        $statistics->setRuntimeMilliseconds(1000);
        $statistics->setNumberOf200(2);
        $statistics->setNumberOf400(4);
        $statistics->setNumberOf500(5);
        $em->persist($statistics);
        $em->flush();

        /** @var TestresultRepository $testresultRepo */
        $testresultRepo = $em->getRepository('AppBundle\Entity\Testresult');
        $iterator = $testresultRepo->getLatestByTestcaseWithStatisticsIterator($testcase, 1);

        $results = [];
        foreach ($iterator as $result) {
            $results[] = $result;
        }

        $this->assertSame(1, sizeof($results));
        $this->assertSame('abc', $results[0][0]['id']);
        $this->assertSame(4, $results[0][1]['numberOf400']);
    }
}
