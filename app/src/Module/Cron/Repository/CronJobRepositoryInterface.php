<?php

declare(strict_types = 1);

namespace App\Module\Cron\Repository;

use App\Module\Cron\Entity\CronJob;

interface CronJobRepositoryInterface
{
    /**
     * @return CronJob[]
     */
    public function findEnabled(): array;
}
