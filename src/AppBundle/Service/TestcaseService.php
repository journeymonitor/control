<?php

namespace AppBundle\Service;

use AppBundle\Entity\Testcase;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcher;

class TestcaseService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $user
     * @param Testcase $testcase
     */
    public function createTestcaseForUser(User $user, Testcase $testcase)
    {
        // The user from the userManager is incomplete in terms of Doctrine,
        // we need a full model from persistance
        $userRepo = $this->entityManager->getRepository('AppBundle\Entity\User');
        $user = $userRepo->findOneBy(['email' => $user->getEmail()]);
        $testcase->setUser($user);
        if ($user->isEnabled()) {
            $testcase->setEnabled(true);
        }
        $this->entityManager->persist($testcase);
        $this->entityManager->flush();
    }
}