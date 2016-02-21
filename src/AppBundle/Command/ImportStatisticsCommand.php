<?php

namespace AppBundle\Command;

use AppBundle\Entity\Statistics;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportStatisticsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('journeymonitor:control:import:statistics')
            ->setDescription('Import statistics from ANALYZE')
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'URL with placeholders :id and :minTestresultDatetimeRun for the endpoint that provides statistics for testcases, e.g. "testcases/:id/statistics/latest?minTestresultDatetimeRun=:minTestresultDatetimeRun"'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $testresultRepo = $em->getRepository('AppBundle\Entity\Testresult');
        $statisticsRepo = $em->getRepository('AppBundle\Entity\Statistics');

        $client = $this->getContainer()->get('guzzle_client');

        // Iterate over testcases
        // Get testresultDatetimeRun value of newest testresult entry with already locally stored statistics
        // Find and replace URL placeholders
        $response = $client->get($input->getArgument('url') . 'statistics/latest?minTestresultDatetimeRun='.urlencode('2016-02-21 22:03:49+0000'));
        $json = $response->json();

        foreach ($json as $statisticsArray) {
            $statistics = $statisticsRepo->findOneBy(['testresult' => $statisticsArray['testresultId']]);
            if (empty($statistics)) {
                try {
                    $testresult = $testresultRepo->find($statisticsArray['testresultId']);
                } catch (\Exception $e) {
                    $output->writeln('Statistics without testresult id:');
                    $output->writeln(print_r($statisticsArray, true));
                }
                if (!empty($testresult)) {
                    $statistics = new Statistics();
                    $statistics->setTestresult($testresult);
                    $statistics->setRuntimeMilliseconds($statisticsArray['runtimeMilliseconds']);
                    $statistics->setNumberOf200($statisticsArray['numberOf200']);
                    $statistics->setNumberOf400($statisticsArray['numberOf400']);
                    $statistics->setNumberOf500($statisticsArray['numberOf500']);
                    $em->persist($statistics);
                    $em->flush($statistics);
                    $output->writeln('Imported statistics for testresult ' . $testresult->getId() . '.');
                } else {
                    $output->writeln(
                        'Could not persist statistics for testresult '
                        . $statisticsArray['testresultId']
                        . ' because the testresult does not exist.');
                }
            } else {
                $output->writeln(
                    'Statistics for testresult '
                    . $statisticsArray['testresultId']
                    . ' are already known.');
            }
        }
        $output->writeln('Import finished.');
    }
}
