<?php

namespace AppBundle\Tests\Functional;

use AppBundle\Tests\TestHelpers;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoggedInLoggedOutFunctionalWebTest extends WebTestCase
{
    use TestHelpers;

    public function testWhenNotLoggedIn()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertContains(
            'JourneyMonitor',
            $crawler->filter('#journeymonitor-header')->first()->text()
        );

        $this->assertSame(
            '/demo/testcases/',
            $crawler->filter('#navbar a:contains("Demo")')->first()->attr('href')
        );

        $this->assertSame(
            '/demo/testcases/',
            $crawler->filter('a:contains("demo user account")')->first()->attr('href')
        );

        $this->assertContains(
            '© ' . date('Y'),
            $crawler->filter('footer')->first()->text()
        );

        $this->assertSame(
            'mailto:replies-welcome@journeymonitor.com',
            $crawler->filter('#journeymonitor-contact-link')->first()->attr('href')
        );
    }

    public function testWhenLoggedIn()
    {
        $this->resetDatabase();
        $client = $this->createAndActivateDemoUser();

        $crawler = $client->request('GET', '/');

        $this->assertSame(302, $client->getResponse()->getStatusCode());

        $this->assertSame(
            '/testcases/',
            $crawler->filter('a:contains("/testcases/")')->first()->attr('href')
        );

        $crawler = $client->request('GET', '/imprint');

        $this->assertSame(
            '/logout',
            $crawler->filter('#navbar a:contains("Logout")')->first()->attr('href')
        );

        $this->assertSame(
            '/testcases/',
            $crawler->filter('#navbar a:contains("Your testcases")')->first()->attr('href')
        );

        $this->assertSame(
            0,
            count($crawler->filter('a:contains("Login")'))
        );
    }
}
