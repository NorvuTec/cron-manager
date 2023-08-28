<?php

namespace Norvutec\CronManagerBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
     * @param string $tag Tag of the Cronjob
     * @return CronJobHistory|null Last completed history of the cron job
     */
    public function getLastCompleted(string $tag): ?CronJobHistory {
        $qb = $this->createQueryBuilder('c');
        $qb->where('c.tag = :tag')
            ->andWhere($qb->expr()->isNotNull('c.exitAt'))
            ->orderBy('c.exitAt', 'DESC')
            ->setParameters([
                'tag' => $tag,
            ])
            ->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Gets the current running history of the cron job
     * Returns null if its not running
     * @param string $tag Tag of the cron job
     * @return CronJobHistory|null The current running history
     */
    public function getCurrentRunning(string $tag): ?CronJobHistory {
        $qb = $this->createQueryBuilder('c');
        $qb->where('c.tag = :tag')
            ->andWhere($qb->expr()->isNull('c.exitAt'))
            ->setParameters([
                'tag' => $tag,
            ])
            ->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Gets the last failed cronjob execution of this job
     * @param string $tag Tag of the cron job
     * @return CronJobHistory|null last failed execution
     */
    public function getLastFailed(string $tag): ?CronJobHistory {
        $qb = $this->createQueryBuilder('c');
        $qb->where('c.tag = :tag')
            ->andWhere($qb->expr()->eq('c.status', ':status'))
            ->orderBy('c.exitAt', 'DESC')
            ->setParameters([
                'tag' => $tag,
                'status' => CronJobStatus::FAILED
            ])
            ->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Gets the last successful cronjob execution of this job
     * @param string $tag Tag of the cron job
     * @return CronJobHistory|null last successful execution
     */
    public function getLastSuccessful(string $tag): ?CronJobHistory {
        $qb = $this->createQueryBuilder('c');
        $qb->where('c.name = :tag')
            ->andWhere($qb->expr()->eq('c.status', ':status'))
            ->orderBy('c.exitAt', 'DESC')
            ->setParameters([
                'tag' => $tag,
                'status' => CronJobStatus::SUCCESS
            ])
            ->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Gets the last cronjob execution of all jobs
     * [Tag] = LastRun
     * @return array
     */
    public function getMappedLatestRuns(): array {
        $qb = $this->createQueryBuilder('c');
        $qb->select('c.tag as job, c.MAX(c.exitAt) as lastRun')
            ->where($qb->expr()->neq('c.status', ":status"))
            ->setParameters([
                'status' => CronJobStatus::RUNNING
            ])
            ->groupBy('c.tag')
            ->orderBy('c.exitAt', 'DESC');
        $data = [];
        foreach($qb->getQuery()->getResult() as $result) {
            $data[$result['job']] = $result['lastRun'];
        }
        return $data;
    }

}