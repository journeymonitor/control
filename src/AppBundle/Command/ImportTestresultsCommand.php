<?php

namespace AppBundle\Command;

use AppBundle\Entity\Testresult;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

// @TODO: Refactor using service, add test
class ImportTestresultsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('journeymonitor:control:import:testresults')
            ->setDescription('Import test results from MONITOR')
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'URL of the endpoint that provides testresults'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $testcaseRepo = $em->getRepository('AppBundle\Entity\Testcase');
        $testresultRepo = $em->getRepository('AppBundle\Entity\Testresult');

        $client = new Client();

        $response = $client->get($input->getArgument('url'));
        $json = $response->json(); // @TODO: This is totally not memory efficient yet

        foreach ($json as $testresultArray) {
            $testresult = $testresultRepo->find($testresultArray['id']);
            if (empty($testresult)) {
                try {
                    $testcase = $testcaseRepo->find($testresultArray['testcaseId']);
                } catch (\Exception $e) {
                    $output->writeln('Testresult ' . $testresultArray['id'] . ' does not have a testcaseId:');
                    $output->writeln(print_r($testresultArray, true));
                }
                if (!empty($testcase)) {
                    $testresult = new Testresult();
                    $testresult->setId($testresultArray['id']);
                    $testresult->setTestcase($testcase);
                    $testresult->setDatetimeRun(new \DateTime($testresultArray['datetimeRun']));
                    $testresult->setExitCode($testresultArray['exitCode']);
                    $testresult->setOutput($testresultArray['output']);
                    $testresult->setFailScreenshotFilename($testresultArray['failScreenshotFilename']);
                    $testresult->setHar($testresultArray['har']);
                    $em->persist($testresult);
                    $em->flush();
                    $output->writeln('Imported testresult ' . $testresult->getId() . '.');
                } else {
                    $output->writeln('Could not persist testresult ' . $testresultArray['id'] . ' because testcase ' . $testresultArray['testcaseId'] .' could not be found.');
                }
            } else {
                $output->writeln('Testresult ' . $testresult->getId() . ' is already known.');
            }
        }
        $output->writeln('Import finished.');
    }
}