<?php

declare(strict_types = 1);

namespace App\Repository;

use App\Module\Cron\Entity\CronJob;
use App\Module\Cron\Entity\CronReport;
use App\Module\Cron\Repository\CronReportRepositoryInterface;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\ManagerRegistry;

final class CronReportRepository extends ServiceEntityRepository implements CronReportRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CronReport::class);
    }

    public function countByJobAndDueTime(CronJob $cronJob, DateTimeImmutable $dueTime): int
    {
        return $this->count(
            [
                'dueTime' => $dueTime,
                'jobId' => $cronJob->getId(),
            ]
        );
    }

    /**
     * @throws ORMException
     */
    public function save(CronReport $cronReport): void
    {
        $em = $this->getEntityManager();

        $em->persist($cronReport);
        $em->flush();
    }
}
