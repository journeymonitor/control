<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Statistics;
use AppBundle\Entity\Testcase;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class TestresultRepository extends EntityRepository
{
    /**
     * @param Testcase $testcase
     * @param int $limit
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult An IterableResult that hydrates arrays
     */
    public function getLatestByTestcaseWithStatisticsIterator(Testcase $testcase, $limit)
    {
        $qb = $this->createQueryBuilder('t');

        $q = $qb
            ->select('t, s')
            ->leftJoin(Statistics::class, 's')
            ->where($qb->expr()->eq('s.testresult', 't.id'))
            ->andWhere($qb->expr()->eq('t.testcase', ':testcase'))
            ->getQuery()
            ->setParameter(':testcase', $testcase)
            ->setMaxResults($limit);

        return $q->iterate(null, Query::HYDRATE_ARRAY);
    }

}
