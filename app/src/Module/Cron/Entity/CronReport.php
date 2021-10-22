<?php

declare(strict_types = 1);

namespace App\Module\Cron\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Table(name="cron_report")
 * @ORM\Entity
 */
class CronReport
{
    /**
     * @ORM\Column(name="id", type="uuid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $dueTime;

    /**
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private bool $finished;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $runDuration;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $exitCode;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $output;

    /**
     * @ORM\Column(type="uuid")
     */
    protected UuidInterface $jobId;

    private DateTimeImmutable $startedAt;

    private function __construct(CronJob $job, DateTimeImmutable $dueTime)
    {
        $this->id = Uuid::uuid4();
        $this->dueTime = $dueTime;
        $this->jobId = $job->getId();
        $this->startedAt = new DateTimeImmutable();
        $this->finished = false;
    }

    public static function start(CronJob $job, DateTimeImmutable $dueTime): self
    {
        $report = new self($job, $dueTime);
        $report->startedAt = new DateTimeImmutable();

        return $report;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getDueTime(): DateTimeImmutable
    {
        return $this->dueTime;
    }

    public function isFinished(): bool
    {
        return $this->finished;
    }

    public function finish(int $exitCode, string $output): void
    {
        $this->finished = true;
        $this->exitCode = $exitCode;
        $this->output = $output;
        $this->runDuration = $this->calculateRunDurationInSeconds();
    }

    public function error(string $output, ?int $exitCode = null): void
    {
        $this->finished = false;
        $this->exitCode = $exitCode;
        $this->output = $output;
        $this->runDuration = $this->calculateRunDurationInSeconds();
    }

    public function getRunDuration(): ?float
    {
        return $this->runDuration;
    }

    public function getExitCode(): ?int
    {
        return $this->exitCode;
    }

    public function getOutput(): ?string
    {
        return $this->output;
    }

    public function getJobId(): UuidInterface
    {
        return $this->jobId;
    }

    private function calculateRunDurationInSeconds(): int
    {
        return (new DateTimeImmutable())->getTimestamp() - $this->startedAt->getTimestamp();
    }
}
