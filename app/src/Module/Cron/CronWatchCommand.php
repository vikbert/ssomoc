<?php

declare(strict_types = 1);

namespace App\Module\Cron;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

final class CronWatchCommand extends Command
{
    private const TIME_LIMIT_IN_SECONDS = 600;
    private const MEMORY_LIMIT_IN_MB = 256;
    private const REPEAT_LIMIT = 20;
    protected static $defaultName = 'cron:watch';
    private $repeatCounter = 0;
    private $startTime;
    private $cronExecutor;

    public function __construct(CronExecutor $cronExecutor)
    {
        parent::__construct();

        $this->cronExecutor = $cronExecutor;
    }

    protected function configure(): void
    {
        $this->setDescription('Watch for cronjobs to be executed');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->startTime = time();
        $io = new SymfonyStyle($input, $output);

        while (true) {
            $io->writeln('Looking for scheduled cron jobs...');

            try {
                $this->assertLimitIsNotReached();
            } catch (Throwable $e) {
                $io->error($e->getMessage());

                return 99;
            }

            try {
                $this->cronExecutor->runScheduledJobs($output);
            } catch (Throwable $e) {
                $io->writeln($e->getMessage());
                $io->writeln($e->getTraceAsString());

                return 1;
            }

            sleep(5);
            ++$this->repeatCounter;
        }
    }

    /**
     * @throws CronException
     */
    private function assertLimitIsNotReached(): void
    {
        if ($this->isMemoryLimitReached()) {
            throw CronException::onMemoryLimitReached();
        }

        if ($this->isTimeLimitReached()) {
            throw CronException::onTimeLimitReached();
        }

        if ($this->isWatcherRepeatLimitReached()) {
            throw CronException::onWatcherRepeatLimitReached();
        }
    }

    private function isMemoryLimitReached(): bool
    {
        return (memory_get_usage(true) / 1024 / 1024) > self::MEMORY_LIMIT_IN_MB;
    }

    private function isTimeLimitReached(): bool
    {
        return (time() - $this->startTime) > self::TIME_LIMIT_IN_SECONDS;
    }

    private function isWatcherRepeatLimitReached(): bool
    {
        return $this->repeatCounter > self::REPEAT_LIMIT;
    }
}
