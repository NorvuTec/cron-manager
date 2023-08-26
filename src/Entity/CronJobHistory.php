<?php

namespace Norvutec\CronManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Norvutec\CronManagerBundle\Model\CronJobStatus;
use Norvutec\CronManagerBundle\Repository\CronJobHistoryRepository;
use Symfony\Component\Console\Command\Command;

#[ORM\Table()]
#[ORM\Entity(repositoryClass: CronJobHistoryRepository::class)]
class CronJobHistory {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

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

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $output = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $error = null;

    public function getId(): ?int
    {
        return $this->id;
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
        return $this;
    }

    public function getOutput(): ?string
    {
        return $this->output;
    }

    public function setOutput(?string $output): self
    {
        $this->output = $output;
        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): self
    {
        $this->error = $error;
        return $this;
    }



}