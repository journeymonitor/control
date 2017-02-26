<?php

namespace Tests\AppBundle\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContentPagesFunctionalWebTest extends WebTestCase
{
    public function testImprint()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $link = $crawler->filter('a:contains("Imprint")')->eq(0)->link();
        $crawler = $client->click($link);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('Impressum', trim($crawler->filter('h3')->eq(0)->text()));
    }
}
