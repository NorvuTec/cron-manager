<?php

namespace Norvutec\CronManagerBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Norvutec\CronManagerBundle\Entity\CronJobHistory;
use Norvutec\CronManagerBundle\Model\CronJobStatus;

/**
 * @extends ServiceEntityRepository<CronJobHistory>
 *
 * @method CronJobHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method CronJobHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method CronJobHistory[]    findAll()
 * @method CronJobHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CronJobHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CronJobHistory::class);
    }

    public function save(CronJobHistory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CronJobHistory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Gets the last completed history of the cron job
     * @param string $name Name of the cron job
     * @return CronJobHistory|null Last completed history of the cron job
     */
    public function getLastCompleted(string $name): ?CronJobHistory {
        $qb = $this->createQueryBuilder('c');
        $qb->where('c.name = :name')
            ->andWhere($qb->expr()->isNotNull('c.exitAt'))
            ->orderBy('c.exitAt', 'DESC')
            ->setParameters([
                'name' => $name,
            ])
            ->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Gets the current running history of the cron job
     * Returns null if its not running
     * @param string $name Name of the cron job
     * @return CronJobHistory|null The current running history
     */
    public function getCurrentRunning(string $name): ?CronJobHistory {
        $qb = $this->createQueryBuilder('c');
        $qb->where('c.name = :name')
            ->andWhere($qb->expr()->isNull('c.exitAt'))
            ->setParameters([
                'name' => $name,
            ])
            ->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Gets the last failed cronjob execution of this job
     * @param string $name Name of the cron job
     * @return CronJobHistory|null last failed execution
     */
    public function getLastFailed(string $name): ?CronJobHistory {
        $qb = $this->createQueryBuilder('c');
        $qb->where('c.name = :name')
            ->andWhere($qb->expr()->eq('c.status', ':status'))
            ->orderBy('c.exitAt', 'DESC')
            ->setParameters([
                'name' => $name,
                'status' => CronJobStatus::FAILED
            ])
            ->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Gets the last successful cronjob execution of this job
     * @param string $name Name of the cron job
     * @return CronJobHistory|null last successful execution
     */
    public function getLastSuccessful(string $name): ?CronJobHistory {
        $qb = $this->createQueryBuilder('c');
        $qb->where('c.name = :name')
            ->andWhere($qb->expr()->eq('c.status', ':status'))
            ->orderBy('c.exitAt', 'DESC')
            ->setParameters([
                'name' => $name,
                'status' => CronJobStatus::SUCCESS
            ])
            ->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }

}