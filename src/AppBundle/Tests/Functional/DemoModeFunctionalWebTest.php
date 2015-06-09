<?php

namespace AppBundle\Tests\Functional;

use AppBundle\Tests\TestHelpers;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DemoModeFunctionalWebTest extends WebTestCase
{
    use TestHelpers;

    public function testDemoMode()
    {
        $this->resetDatabase();
        $client = $this->createAndActivateDemoUser();

        $crawler = $client->request('GET', '/demo/testcases/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals('You are currently in demo mode.', trim($crawler->filter('div.alert.alert-info')->first()->text()));
        $this->assertEquals('Demo User Testcase One', trim($crawler->filter('h4')->eq(1)->text()));
        $this->assertEquals('Not available in demo mode', trim($crawler->filter('div.row div.col-xs-12 a.pull-right')->first()->attr('title')));
    }

    public function testNotDemoMode()
    {
        $this->resetDatabase();
        $this->createAndActivateDemoUser();

        $client = static::createClient();
        $client->request('GET', '/testcases/');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}
