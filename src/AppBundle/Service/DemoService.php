<?php

namespace AppBundle\Service;

use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class DemoService
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var string
     */
    private $demoUserEmail;

    /**
     * @var string
     */
    private $demoPath;

    /**
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager, $demoUserEmail, $demoPath)
    {
        $this->userManager = $userManager;
        $this->demoUserEmail = $demoUserEmail;
        $this->demoPath = $demoPath;
    }

    public function isDemoMode(Request $request) {
        return substr($request->getPathInfo(), 0, strlen($this->demoPath)) === $this->demoPath;
    }

    /**
     * Dependending on the request, either return the demo user or the one passed in
     *
     * @param Request $request
     * @param mixed $realUser
     * @return \FOS\UserBundle\Model\UserInterface
     */
    public function getUser(Request $request, $realUser)
    {
        if ($this->isDemoMode($request)) {
            $demoUser = $this->userManager->findUserByEmail($this->demoUserEmail);
            if (empty($demoUser)) {
                return $realUser;
            } else {
                return $demoUser;
            }
        } else {
            return $realUser;
        }
    }
}
