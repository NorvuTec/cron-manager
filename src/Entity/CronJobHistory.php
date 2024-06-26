<?php

namespace Norvutec\CronManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Norvutec\CronManagerBundle\Model\CronJobStatus;
use Norvutec\CronManagerBundle\Repository\CronJobHistoryRepository;

#[ORM\Table(name: 'cronjob_history')]
#[ORM\Entity(repositoryClass: CronJobHistoryRepository::class)]
class CronJobHistory {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $tag = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', enumType: CronJobStatus::class)]
    private CronJobStatus $status = CronJobStatus::UNKNOWN;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $host = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $runAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $exitAt = null;

    #[ORM\Column(type: 'float')]
    private float $executionTime = 0.0;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $exitCode = null;

    private array $output = [];

    #[ORM\Column]
    private array $error = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(?string $tag): self
    {
        $this->tag = $tag;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getStatus(): CronJobStatus
    {
        return $this->status;
    }

    public function setStatus(CronJobStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(?string $host): self
    {
        $this->host = $host;
        return $this;
    }

    public function getRunAt(): \DateTime
    {
        return $this->runAt;
    }

    public function setRunAt(\DateTime $runAt): self
    {
        $this->runAt = $runAt;
        return $this;
    }

    public function getExitAt(): ?\DateTime
    {
        return $this->exitAt;
    }

    public function setExitAt(?\DateTime $exitAt): self
    {
        $this->exitAt = $exitAt;
        $this->setExecutionTime(($exitAt->getTimestamp() - $this->getRunAt()->getTimestamp()));
        return $this;
    }

    public function getExecutionTime(): float
    {
        return $this->executionTime;
    }

    public function setExecutionTime(float $executionTime): self
    {
        $this->executionTime = $executionTime;
        return $this;
    }

    public function getExitCode(): int
    {
        return $this->exitCode;
    }

    public function setExitCode(int $exitCode): self
    {
        $this->exitCode = $exitCode;
        if($this->getStatus() == CronJobStatus::RUNNING) {
            if ($exitCode == 99) {
                $this->setStatus(CronJobStatus::UNKNOWN);
            } elseif ($exitCode == 0) {
                $this->setStatus(CronJobStatus::SUCCESS);
            } else {
                $this->setStatus(CronJobStatus::FAILED);
            }
        }
        return $this;
    }

    public function getOutput(): array
    {
        return $this->output;
    }

    public function addOutput(string $output): self
    {
        $this->output[] = $output;
        return $this;
    }

    public function setOutput(array $output): self
    {
        $this->output = $output;
        return $this;
    }

    public function getError(): array
    {
        return $this->error;
    }

    public function addError(string $error): self
    {
        $this->error[] = $error;
        return $this;
    }

    public function setError(array $error): self
    {
        $this->error = $error;
        return $this;
    }



}