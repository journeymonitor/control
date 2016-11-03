<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Service\RegistrationService;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;

class RegistrationServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\FOS\UserBundle\Doctrine\UserManager
     */
    private $userManagerMock;
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\FOS\UserBundle\Security\LoginManager
     */
    private $loginManagerMock;
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Security\Core\Encoder\EncoderFactory
     */
    private $encoderFactoryMock;
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\HttpFoundation\Session\Session
     */
    private $sessionMock;
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage
     */
    private $tokenStorageMock;
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|EventDispatcher
     */
    private $dispatcherMock;
    /**+
     * @var RegistrationService
     */
    private $service;
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Form\Form
     */
    private $formMock;
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\HttpFoundation\Session\Flash\FlashBag
     */
    private $flashBagMock;

    public function setUp()
    {
        $this->userManagerMock = $this->getMockBuilder('FOS\UserBundle\Doctrine\UserManager')
            ->disableOriginalConstructor()->getMock();
        $this->loginManagerMock = $this->getMockBuilder('Tests\AppBundle\Stubs\LoginManagerStub')
            ->disableOriginalConstructor()->getMock();
        $this->encoderFactoryMock = $this->getMockBuilder('Symfony\Component\Security\Core\Encoder\EncoderFactory')
            ->disableOriginalConstructor()->getMock();
        $this->sessionMock = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session\Session')
            ->disableOriginalConstructor()->getMock();
        $this->dispatcherMock = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcher')
            ->disableOriginalConstructor()->getMock();
        $this->formMock = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()->getMock();
        $this->flashBagMock = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session\Flash\FlashBag')
            ->disableOriginalConstructor()->getMock();

        $this->sessionMock->expects($this->any())
            ->method('getFlashBag')
            ->willReturn($this->flashBagMock);

        $this->service = new RegistrationService(
            $this->userManagerMock,
            $this->loginManagerMock,
            $this->encoderFactoryMock,
            $this->sessionMock,
            $this->dispatcherMock
        );
    }

    public function testCreateUserOrLoginCreate()
    {
        $user = new User();
        $this->formMock->expects($this->once())
            ->method('get')
            ->with('user')
            ->willReturn($this->formMock);
        $this->flashBagMock->expects($this->once())
            ->method('add');
        $this->service->createUserOrLogin($user, $this->formMock);
    }

    public function testCreateUserOrLoginCreateLoginValidAndUserEnabled()
    {
        $user = new User();
        $user->setEmail('test@test.test');
        $user->setPassword('testing');

        $existingUser = new User();
        $existingUser->setPassword('testing');
        $existingUser->setSalt('');
        $existingUser->setEnabled(true);
        $this->userManagerMock->expects($this->once())
            ->method('findUserByEmail')
            ->with('test@test.test')
            ->willReturn($existingUser);

        $this->encoderFactoryMock->expects($this->once())
            ->method('getEncoder')
            ->willReturn(new PlaintextPasswordEncoder());
        $this->loginManagerMock->expects($this->once())
            ->method('loginUser')
            ->with('main', $existingUser);
        $this->service->createUserOrLogin($user, $this->formMock);
    }

    public function testCreateUserOrLoginCreateLoginValidAndUserNotYetEnabled()
    {
        $user = new User();
        $user->setEmail('test@test.test');
        $user->setPassword('testing');

        $existingUser = new User();
        $existingUser->setPassword('testing');
        $existingUser->setSalt('');
        $this->userManagerMock->expects($this->once())
            ->method('findUserByEmail')
            ->with('test@test.test')
            ->willReturn($existingUser);

        $this->encoderFactoryMock->expects($this->once())
            ->method('getEncoder')
            ->willReturn(new PlaintextPasswordEncoder());
        $this->loginManagerMock->expects($this->never())
            ->method('loginUser');
        $this->service->createUserOrLogin($user, $this->formMock);
    }

    public function testCreateUserOrLoginCreateLoginInvalid()
    {
        $user = new User();
        $user->setEmail('test@test.test');
        $user->setPassword('testingwrong');

        $existingUser = new User();
        $existingUser->setPassword('testing');
        $existingUser->setSalt('');
        $this->userManagerMock->expects($this->once())
            ->method('findUserByEmail')
            ->with('test@test.test')
            ->willReturn($existingUser);

        $this->encoderFactoryMock->expects($this->once())
            ->method('getEncoder')
            ->willReturn(new PlaintextPasswordEncoder());

        $this->setExpectedException(
            '\Symfony\Component\Security\Core\Exception\AuthenticationException'
        );
        $this->service->createUserOrLogin($user, $this->formMock);
    }
}
