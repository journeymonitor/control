<?php

namespace AppBundle\Command;

use AppBundle\Entity\Statistic;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
                'URL of the endpoint that provides statistics'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $testresultRepo = $em->getRepository('AppBundle\Entity\Testresult');
        $statisticRepo = $em->getRepository('AppBundle\Entity\Statistic');

        $client = $this->getContainer()->get('guzzle_client');

        $response = $client->get($input->getArgument('url'));
        $json = $response->json(); // @TODO: This is totally not memory efficient yet

        foreach ($json as $statisticArray) {
            $statistic = $statisticRepo->findOneBy(['testresult' => $statisticArray['testresultId']]);
            if (empty($statistic)) {
                try {
                    $testresult = $testresultRepo->find($statisticArray['testresultId']);
                } catch (\Exception $e) {
                    $output->writeln('Statistics without testresult id:');
                    $output->writeln(print_r($statisticArray, true));
                }
                if (!empty($testresult)) {
                    $statistic = new Statistic();
                    $statistic->setTestresult($testresult);
                    $statistic->setRuntimeMilliseconds($statisticArray['runtimeMilliseconds']);
                    $statistic->setNumberOf200($statisticArray['numberOf200']);
                    $statistic->setNumberOf400($statisticArray['numberOf400']);
                    $statistic->setNumberOf500($statisticArray['numberOf500']);
                    $em->persist($statistic);
                    $em->flush();
                    $output->writeln('Imported statistics for testresult ' . $testresult->getId() . '.');
                } else {
                    $output->writeln('Could not persist statistics for testresult ' . $statisticArray['testresultId'] . ' because the testresult does not exist.');
                }
            } else {
                $output->writeln('Statistics for testresult ' . $statisticArray['testresultId'] . ' are already known.');
            }
        }
        $output->writeln('Import finished.');
    }
}