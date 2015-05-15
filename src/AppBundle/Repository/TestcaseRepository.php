<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Testcase;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class TestcaseRepository extends EntityRepository
{
    /**
     * @param User $user
     * @return Testcase[]|ArrayCollection
     */
    public function findWaitingConfirmation(User $user)
    {
        return $this->createQueryBuilder('t')
            ->where('t.enabled = 0 AND t.createdAt <= :userActivatedAt')
            ->setParameter('userActivatedAt', $user->getActivatedAt())
            ->getQuery()->getResult();
    }
}