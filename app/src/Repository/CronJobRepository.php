<?php

declare(strict_types = 1);

namespace App\Repository;

use App\Module\Cron\Entity\CronJob;
use App\Module\Cron\Repository\CronJobRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CronJob[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class CronJobRepository extends ServiceEntityRepository implements CronJobRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CronJob::class);
    }

    /**
     * @return CronJob[]
     */
    public function findEnabled(): array
    {
        return $this->findBy(['enabled' => true]);
    }
}
