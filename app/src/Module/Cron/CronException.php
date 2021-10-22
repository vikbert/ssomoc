<?php

declare(strict_types = 1);

namespace App\Module\Cron;

use Exception;

final class CronException extends Exception
{
    public static function onMemoryLimitReached(): self
    {
        return new self('CRON_WATCHER: Memory limit reached. Stopping cron watch command.');
    }

    public static function onTimeLimitReached(): self
    {
        return new self('CRON_WATCHER: Watch time limit reached. Stopping cron watch command.');
    }

    public static function onWatcherRepeatLimitReached(): self
    {
        return new self('CRON_WATCHER: Watch loop limit reached. Stopping cron watch command.');
    }
}
