<?php
namespace AppBundle\Service;

use AppBundle\Entity\Testcase;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Mailer\Mailer;
use FOS\UserBundle\Security\LoginManager;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcher;

class TestcaseService
{
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var Mailer
     */
    private $mailer;
    /**
     * @var LoginManager
     */
    private $loginManager;
    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @param UserManager $userManager
     * @param Mailer $mailer
     * @param LoginManager $loginManager
     * @param TraceableEventDispatcher $dispatcher
     * @param EntityManager $entityManager
     */
    public function __construct(
        UserManager $userManager,
        Mailer $mailer,
        LoginManager $loginManager,
        TraceableEventDispatcher $dispatcher,
        EntityManager $entityManager
    )
    {
        $this->userManager = $userManager;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->loginManager = $loginManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param User $user
     * @param Testcase $testcase
     */
    public function createTestcaseAndUser(User $user, Testcase $testcase)
    {
        if ($existingUser = $this->userManager->findUserByEmail($user->getEmail()))
        {
            $this->loginManager->loginUser('main', $user);
        } else {
            $user->setUsername($user->getEmail());
            $this->userManager->updatePassword($user);
            $this->userManager->updateUser($user);
            $this->dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS);
        }

        $testcase->setUser($user);
        $this->entityManager->persist($testcase);
        $this->entityManager->flush();
    }
}