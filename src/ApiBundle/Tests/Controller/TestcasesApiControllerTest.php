<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Testcase;
use AppBundle\Entity\User;
use AppBundle\Tests\TestHelpers;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestcasesApiControllerWebTest extends WebTestCase
{
    use TestHelpers;

    public function testRouteIsProtected()
    {
        $client = static::createClient();

        $client->request('GET', '/api/internal/testcases.json');
        $this->assertSame(401, $client->getResponse()->getStatusCode());
        $this->assertSame('', $client->getResponse()->getContent());

        $client->request('GET', '/api/internal/testcases');
        $this->assertSame(401, $client->getResponse()->getStatusCode());
        $this->assertSame('', $client->getResponse()->getContent());

        $client->request('GET', '/api/internal/testcases.xml');
        $this->assertSame(401, $client->getResponse()->getStatusCode());
        $this->assertSame('', $client->getResponse()->getContent());

        $client->request('GET', '/api/internal/testcases/');
        $this->assertSame(404, $client->getResponse()->getStatusCode());
    }

    public function testRouteIsAccessibleWithBasicAuth()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'internal_api',
            'PHP_AUTH_PW'   => '2b51865a16984a18af71f1bd64ffff8c',
        ));

        $client->request('GET', '/api/internal/testcases.json');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testIndex()
    {
        $this->resetDatabase();

        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'internal_api',
            'PHP_AUTH_PW'   => '2b51865a16984a18af71f1bd64ffff8c',
        ));

        $container = $client->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');

        $user = new User();
        $user->setEmail('user@example.org');
        $user->setUsername($user->getEmail());
        $user->setPassword('abc');
        $user->setSalt('xyz');
        $user->setActivatedAt(new \DateTime());
        $em->persist($user);

        $testcase = new Testcase();
        $testcase->setTitle('Test One');
        $testcase->setUser($user);
        $testcase->setCadence('*/15');
        $testcase->setEnabled(true);
        $testcase->setScript('foo');
        $em->persist($testcase);

        $testcase = new Testcase();
        $testcase->setTitle('Test Two');
        $testcase->setUser($user);
        $testcase->setCadence('*/5');
        $testcase->setEnabled(false);
        $testcase->setScript('bar');
        $em->persist($testcase);

        $em->flush();

        $client->request('GET', '/api/internal/testcases.json');

        $content = $client->getResponse()->getContent();

        $structuredContent = json_decode($content);

        $this->assertSame(1, sizeof($structuredContent));
        $this->assertSame('Test One', $structuredContent[0]->title);
        $this->assertSame('user@example.org', $structuredContent[0]->notifyEmail);
        $this->assertSame('*/15', $structuredContent[0]->cadence);
        $this->assertSame('foo', $structuredContent[0]->script);
    }
}
