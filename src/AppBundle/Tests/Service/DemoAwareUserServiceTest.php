<?php
namespace AppBundle\Tests\Service;

use AppBundle\Entity\User;
use AppBundle\Service\DemoAwareUserService;
use AppBundle\Service\RegistrationService;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;

class DemoAwareUserServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\FOS\UserBundle\Doctrine\UserManagerInterface
     */
    private $userManagerMock;

    public function setUp()
    {
        $this->userManagerMock = $this->getMockBuilder('FOS\UserBundle\Doctrine\UserManager')
            ->disableOriginalConstructor()->getMock();
    }

    public function testThatDemoUserIsReturned()
    {
        $this->userManagerMock->expects($this->once())
            ->method('findUserByEmail')
            ->with('demo-user@journeymonitor.com')
            ->willReturn('demo');

        $request = Request::create('/demo/testcases/', 'GET');

        $daus = new DemoAwareUserService(
            $this->userManagerMock,
            'demo-user@journeymonitor.com',
            '/demo/'
        );

        $this->assertSame('demo', $daus->getUser($request, 'real'));
    }

    public function testThatDemoUserIsNotReturned()
    {
        $this->userManagerMock->expects($this->never())
            ->method('findUserByEmail');

        $request = Request::create('/testcases/', 'GET');

        $daus = new DemoAwareUserService(
            $this->userManagerMock,
            'demo-user@journeymonitor.com',
            '/demo/'
        );

        $this->assertSame('real', $daus->getUser($request, 'real'));
    }

    public function testThatRealUserIsReturnedIfDemoUserNotFound()
    {
        $this->userManagerMock->expects($this->once())
            ->method('findUserByEmail')
            ->with('demo-user@journeymonitor.com')
            ->willReturn(null);

        $request = Request::create('/demo/testcases/', 'GET');

        $daus = new DemoAwareUserService(
            $this->userManagerMock,
            'demo-user@journeymonitor.com',
            '/demo/'
        );

        $this->assertSame('real', $daus->getUser($request, 'real'));
    }
}