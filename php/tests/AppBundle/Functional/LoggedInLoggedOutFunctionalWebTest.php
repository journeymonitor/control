<?php

namespace Tests\AppBundle\Functional;

use Tests\AppBundle\TestHelpers;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoggedInLoggedOutFunctionalWebTest extends WebTestCase
{
    use TestHelpers;

    public function testWhenNotLoggedIn()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertNotContains(
            'Logged in as',
            $crawler->filter('body')->first()->text()
        );
    }

    public function testWhenLoggedIn()
    {
        $this->resetDatabase();
        $client = $this->createAndActivateDemoUser();

        $client->request('GET', '/');

        $this->assertSame(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();

        $this->assertContains(
            'Logged in as',
            $crawler->filter('#loggedin-info')->first()->text()
        );

        $this->assertContains(
            'demo-user@journeymonitor.com',
            $crawler->filter('#loggedin-info')->first()->text()
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
