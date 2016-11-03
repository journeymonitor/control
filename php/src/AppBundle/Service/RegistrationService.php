<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use FOS\UserBundle\Doctrine\UserManager;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Security\LoginManager;
use FOS\UserBundle\Security\LoginManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class RegistrationService
{
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var LoginManager
     */
    private $loginManager;
    /**
     * @var EncoderFactory
     */
    private $encoderFactory;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @param UserManager $userManager
     * @param LoginManagerInterface $loginManager
     * @param EncoderFactory $encoderFactory
     * @param Session $session
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        UserManager $userManager,
        LoginManagerInterface $loginManager,
        EncoderFactory $encoderFactory,
        Session $session,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->userManager = $userManager;
        $this->loginManager = $loginManager;
        $this->encoderFactory = $encoderFactory;
        $this->session = $session;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param User $user
     * @param FormInterface $regForm
     * @return User
     */
    public function createUserOrLogin(User $user, FormInterface $regForm)
    {
        $existingUser = $this->userManager->findUserByEmail($user->getEmail());
        if (!empty($existingUser)) {
            $encoder = $this->encoderFactory->getEncoder($existingUser);

            if (!$encoder->isPasswordValid(
                $existingUser->getPassword(), $user->getPassword(), $existingUser->getSalt()
            ))  {
                throw new AuthenticationException();
            }
            if ($existingUser->isEnabled()) {
                $this->loginManager->loginUser('main', $existingUser);
                $user = $existingUser;
            }
        } else {
            $this->createUserEntry($user);
            $event = new FormEvent($regForm->get('user'), new Request());
            $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
        }
        return $user;
    }

    /**
     * @param User $user
     */
    private function createUserEntry(User $user)
    {
        $user->setUsername($user->getEmail());
        $user->setPlainPassword($user->getPassword());
        $this->userManager->updatePassword($user);
        $this->userManager->updateUser($user);
        $this->session->getFlashBag()->add('success', 'Thank you. Please check your mails in order to activate your account.');
    }
}
