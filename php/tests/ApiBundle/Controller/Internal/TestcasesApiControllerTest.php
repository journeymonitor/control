<?php

namespace Tests\ApiBundle\Controller\Internal;

use AppBundle\Entity\Testcase;
use AppBundle\Entity\User;
use Tests\AppBundle\TestHelpers;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestcasesApiControllerWebTest extends WebTestCase
{
    use TestHelpers;

    public function testIndex()
    {
        $this->resetDatabase();

        $client = static::createClient();

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
