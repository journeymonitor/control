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
        $testcase->setUser($user);
        $this->entityManager->persist($testcase);
        $this->entityManager->flush();
    }
}