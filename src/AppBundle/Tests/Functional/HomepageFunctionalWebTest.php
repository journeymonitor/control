<?php

namespace AppBundle\Tests\Functional;

use AppBundle\Tests\TestHelpers;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomepageFunctionalWebTest extends WebTestCase
{
    use TestHelpers;

    public function testContents()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertContains(
            'JourneyMonitor',
            $crawler->filter('a.brandname')->first()->text()
        );

        $this->assertSame(
            '/demo/testcases/',
            $crawler->filter('#navbar a:contains("Demo")')->first()->attr('href')
        );

        $this->assertSame(
            '/demo/testcases/',
            $crawler->filter('a:contains("demo user account")')->first()->attr('href')
        );

        $this->assertTrue(
            $crawler->filter('#testcase_and_user_user_password')->attr('type') == 'password'
        );

        $this->assertContains(
            '© ' . date('Y'),
            $crawler->filter('footer')->first()->text()
        );

        $this->assertSame(
            'mailto:replies-welcome@journeymonitor.com',
            $crawler->filter('a:contains("Get in touch")')->first()->attr('href')
        );
    }
}
