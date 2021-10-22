<?php

declare(strict_types = 1);

namespace App\Module\Cron\Factory;

use App\Module\Cron\Entity\CronJob;

final class CronJobFactory
{
    public static function cacheClear(): CronJob
    {
        return new CronJob(
            'cache:clear',
            '*/5 * * * *',
            true,
            'cache clear demo cron job'
        );
    }

    public static function dummyFoo(): CronJob
    {
        return new CronJob(
            'dummy:foo',
            '*/5 * * * *',
            true,
            'dummy command foo'
        );
    }

    public static function dummyBar(): CronJob
    {
        return new CronJob(
            'dummy:bar',
            '*/5 * * * *',
            true,
            'dummy command bar'
        );
    }
}
