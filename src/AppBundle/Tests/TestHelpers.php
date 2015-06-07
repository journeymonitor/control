<?php

namespace AppBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

trait TestHelpers
{
    protected function resetDatabase()
    {
        $client = static::createClient();
        $kernel = $client->getKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArgvInput(['', 'doctrine:database:drop', '--no-interaction', '--force', '-q']);
        $application->run($input);

        $output = new ConsoleOutput();
        $input = new ArgvInput(['', 'doctrine:migrations:migrate', '--no-interaction', '-q']);
        $application->run($input, $output);
    }

    /**
     * @return \Symfony\Bundle\FrameworkBundle\Client A client where the demo user is already logged in
     */
    protected function createAndActivateDemoUser()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $buttonNode = $crawler->selectButton('Start monitoring');

        $form = $buttonNode->form();

        $client->submit($form, array(
            'testcase_and_user[user][email]' => 'demo-user@journeymonitor.com',
            'testcase_and_user[user][password]' => 'foo',
            'testcase_and_user[testcase][title]' => 'Demo User Testcase One',
            'testcase_and_user[testcase][cadence]' => '*/5',
            'testcase_and_user[testcase][script]' => 'bar',
        ));

        $container = $client->getContainer();
        $um = $container->get('fos_user.user_manager');

        $user = $um->findUserBy(['email' => 'demo-user@journeymonitor.com']);
        $client->request('GET', '/register/confirm/' . $user->getConfirmationToken());
        return $client;
    }
}
