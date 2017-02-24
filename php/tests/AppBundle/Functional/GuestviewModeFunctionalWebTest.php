<?php

namespace Tests\AppBundle\Functional;

use Tests\AppBundle\TestHelpers;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GuestviewModeFunctionalWebTest extends WebTestCase
{
    use TestHelpers;

    public function testGuestviewMode()
    {
        $this->resetDatabase();
        $client = $this->getClientThatRegisteredAndActivatedADemoUser();

        // This is the testcases page of the demo user, on which the Guest View link must be shown to the logged in demo user
        $crawler = $client->request('GET', '/testcases/');
        $link = $crawler->filter('a:contains("Guest View link for this overview")')->eq(0)->link();

        // We need to create a new client because we want to verify that we can visit the guest view page without being logged in
        $client = static::createClient();
        $crawler = $client->click($link);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertNotContains(
            'Logged in as',
            $crawler->filter('body')->first()->text()
        );

        $this->assertEquals(
            'You are viewing this page in Guest View mode. Some functions on this page are not available to you.',
            trim($crawler->filter('div.alert.alert-warning')->first()->text())
        );
        $this->assertEquals('Demo User Testcase One', trim($crawler->filter('h4')->eq(1)->text()));
        $this->assertEquals(
            'Not available in demo and guest view mode',
            trim($crawler->filter('div.row div.col-xs-12 a.pull-right')->first()->attr('title'))
        );

        // Verifying that a Guest View URI with an invalid security token doesn't give access to the testcases page.
        $linkUri = $link->getUri();
        $linkUriWithWrongGuestviewSecurityToken = \str_replace(
            'guestviewSecurityToken=',
            'guestviewSecurityToken=a',
            $linkUri
        );
        $crawler = $client->request('GET', $linkUriWithWrongGuestviewSecurityToken);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertContains(
            'Redirecting to /.',
            $crawler->filter('body')->first()->text()
        );

        $crawler = $client->followRedirect();

        $this->assertContains(
            'Guest view access denied.',
            $crawler->filter('div.alert-danger')->first()->text()
        );
    }

}
