<?php
namespace AppBundle\Service;


use AppBundle\Entity\User;
use FOS\UserBundle\Doctrine\UserManager;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Security\LoginManager;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;

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
     * @var TokenStorage
     */
    private $tokenStorage;
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @param UserManager $userManager
     * @param LoginManager $loginManager
     * @param EncoderFactory $encoderFactory
     * @param Session $session
     * @param TokenStorage|SessionTokenStorage $tokenStorage
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(
        UserManager $userManager,
        LoginManager $loginManager,
        EncoderFactory $encoderFactory,
        Session $session,
        TokenStorage $tokenStorage,
        EventDispatcher $eventDispatcher
    )
    {
        $this->userManager = $userManager;
        $this->loginManager = $loginManager;
        $this->encoderFactory = $encoderFactory;
        $this->session = $session;
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createUserOrLogin(User $user, FormInterface $regForm)
    {
        $existingUser = $this->userManager->findUserByEmail($user->getEmail());
        if (!empty($existingUser)) {
            $encoder = $this->encoderFactory->getEncoder($existingUser);

            if (!$encoder->isPasswordValid(
                $existingUser->getPassword(),$user->getPassword(),$existingUser->getSalt()
            ))  {
                throw new AuthenticationException();
            }

            $this->loginManager->loginUser('main', $user);

        } else {
            $user->setUsername($user->getEmail());
            $user->setPlainPassword($user->getPassword());
            $this->userManager->updatePassword($user);
            $this->userManager->updateUser($user);
            $this->session->getFlashBag()->add('success', 'Please confirm your email address to activate your monitoring');
            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->tokenStorage->setToken($token);
            $this->session->set('_security_main', serialize($token));
            $event = new FormEvent($regForm->get('user'), new Request());
            $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
        }
        return $user;
    }
}