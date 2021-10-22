<?php

declare(strict_types = 1);

namespace App\Module\Cron\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;

/**
 * @ORM\Table(name="cron_job")
 * @ORM\Entity
 */
class CronJob
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(length=128, unique=true )
     */
    private string $name;

    /**
     * @ORM\Column(length=1024, unique=true)
     */
    private string $command;

    /**
     * @ORM\Column(length=128)
     */
    private string $schedule;

    /**
     * @ORM\Column(length=128, nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private bool $enabled;

    public function __construct(string $name, string $command, string $schedule, bool $enabled, ?string $description = null)
    {
        $this->id = Uuid::uuid4();
        $this->name = $name;
        $this->command = trim($command);
        $this->schedule = trim($schedule);
        $this->enabled = $enabled;
        $this->description = $description;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    private function validateCommandCharacters(): void
    {
        $forbiddenChars = ['&', '|', ';'];

        $cleanCommand = str_replace($forbiddenChars, '', $this->command);

        if ($cleanCommand !== $this->command) {
            throw new RuntimeException(sprintf('Forbidden characters %s found in job command: %s.', json_encode($forbiddenChars), $this->command));
        }
    }

    public function getCommand(): string
    {
        $this->validateCommandCharacters();

        return $this->command;
    }

    public function getSchedule(): string
    {
        return $this->schedule;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
