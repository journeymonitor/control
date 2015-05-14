<?php
namespace AppBundle\Service;


use AppBundle\Entity\User;
use FOS\UserBundle\Doctrine\UserManager;
use FOS\UserBundle\Security\LoginManager;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

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
     * @param UserManager $userManager
     * @param LoginManager $loginManager
     * @param EncoderFactory $encoderFactory
     */
    public function __construct(
        UserManager $userManager,
        LoginManager $loginManager,
        EncoderFactory $encoderFactory
    )
    {
        $this->userManager = $userManager;
        $this->loginManager = $loginManager;
        $this->encoderFactory = $encoderFactory;
    }

    public function createUserOrLogin(User $user)
    {
        $existingUser = $this->userManager->findUserByEmail($user->getEmail());
        if (!empty($existingUser)) {
            $encoder = $this->encoderFactory->getEncoder($existingUser);

            if ($encoder->isPasswordValid(
                $existingUser->getPassword(),$user->getPassword(),$existingUser->getSalt()
            ))  {
                $this->loginManager->loginUser('main', $user);
                return true;
            } else {
                return false;
            }
        } else {
            $user->setUsername($user->getEmail());
            $this->userManager->updatePassword($user);
            $this->userManager->updateUser($user);
            return $user;
        }
    }
}