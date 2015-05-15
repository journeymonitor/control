<?php
namespace AppBundle\Tests\Stubs;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Security\LoginManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class LoginManagerStub implements LoginManagerInterface
{
    public function loginUser($firewallName, UserInterface $user, Response $response = null)
    {

    }
}