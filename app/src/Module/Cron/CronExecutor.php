<?php

declare(strict_types = 1);

namespace App\Module\Cron;

use Ahc\Cron\Expression;
use App\Module\Cron\Entity\CronJob;
use App\Module\Cron\Entity\CronReport;
use App\Module\Cron\Repository\CronJobRepositoryInterface;
use App\Module\Cron\Repository\CronReportRepositoryInterface;
use DateTime;
use DateTimeImmutable;
use RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;
use Throwable;

final class CronExecutor
{
    /**
     * To ensure we don't miss any jobs due to any latencies (i.e. process / container restart)
     * we provide a time tolerance to the scheduler.
     */
    private const CRON_TOLERANCE_MINUTES = 5;

    private $kernel;
    private $cronJobRepository;
    private $cronReportRepository;

    public function __construct(
        KernelInterface $kernel,
        CronJobRepositoryInterface $cronJobRepository,
        CronReportRepositoryInterface $cronReportRepository
    ) {
        $this->kernel = $kernel;
        $this->cronJobRepository = $cronJobRepository;
        $this->cronReportRepository = $cronReportRepository;
    }

    public function runScheduledJobs(?OutputInterface $output = null): OutputInterface
    {
        $output = $output ?? new BufferedOutput();

        $io = new SymfonyStyle(new ArrayInput([]), $output);

        $jobs = $this->cronJobRepository->findEnabled();
        if (0 === count($jobs)) {
            $io->warning('No enabled jobs have been found.');

            return $output;
        }

        $io->success(sprintf('Found %d enabled job(s).', count($jobs)));

        $referenceTime = $this->createReferenceDateTime();
        $jobsExecuted = 0;
        foreach ($jobs as $job) {
            for ($i = 0; $i < self::CRON_TOLERANCE_MINUTES; ++$i) {
                $dueTime = $referenceTime->modify(sprintf('-%d minutes', $i));

                if (!$this->jobIsScheduled($job, $dueTime)) {
                    continue;
                }

                if ($this->jobAlreadyStarted($job, $dueTime)) {
                    $io->writeln(
                        sprintf(
                            'Job "%s" has already been started: %s ',
                            $job->getId()->toString(),
                            $job->getName()
                        )
                    );
                    continue;
                }

                $io->comment(
                    sprintf(
                        'Starting job "%s" (%s):',
                        $job->getName(),
                        $job->getId()->toString()
                    )
                );

                $this->execute($job, $dueTime, $output);
                ++$jobsExecuted;

                $io->comment(
                    sprintf(
                        'Finished job "%s" (%s).',
                        $job->getName(),
                        $job->getId()->toString()
                    )
                );

                break;
            }
        }

        if (0 === $jobsExecuted) {
            $io->warning('No scheduled jobs have been found.');
        }

        return $output;
    }

    private function createReferenceDateTime(): DateTimeImmutable
    {
        $now = new DateTime();
        $now->setTime((int) $now->format('H'), (int) $now->format('i'), 0);

        return DateTimeImmutable::createFromMutable($now);
    }

    private function jobIsScheduled(CronJob $job, DateTimeImmutable $dueTime): bool
    {
        return Expression::isDue($job->getSchedule(), $dueTime->getTimestamp());
    }

    private function jobAlreadyStarted(CronJob $job, DateTimeImmutable $dueTime): bool
    {
        return 0 < $this->cronReportRepository->countByJobAndDueTime($job, $dueTime);
    }

    private function execute(CronJob $job, DateTimeImmutable $dueTime, OutputInterface $output): void
    {
        $report = CronReport::start($job, $dueTime);
        $this->cronReportRepository->save($report);

        try {
            $process = Process::fromShellCommandline($this->commandWithAbsolutionPath($job));
            $process->start();
            $exitCode = $process->wait();

            if (!$process->isSuccessful()) {
                $errorOutput = $process->getErrorOutput();
                $output->write($errorOutput);

                throw new RuntimeException('[Process error] ' . $errorOutput);
            }

            $report->finish($exitCode, $process->getOutput());
        } catch (Throwable $e) {
            $report->error($e->getMessage() . ':' . PHP_EOL . $e->getTraceAsString());
        }

        $this->cronReportRepository->save($report);
    }

    private function commandWithAbsolutionPath(CronJob $job): string
    {
        return sprintf(
            '%s/bin/console %s',
            $this->kernel->getProjectDir(),
            $job->getCommand()
        );
    }
}
