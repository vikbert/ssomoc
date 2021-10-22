<?php

declare(strict_types = 1);

namespace App\Module\Cron\Repository;

use App\Module\Cron\Entity\CronJob;
use App\Module\Cron\Entity\CronReport;
use DateTimeImmutable;

interface CronReportRepositoryInterface
{
    public function countByJobAndDueTime(CronJob $cronJob, DateTimeImmutable $dueTime): int;

    public function save(CronReport $cronReport): void;
}
