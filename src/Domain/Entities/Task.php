<?php declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'task')]
#[ORM\Entity(repositoryClass: 'App\Domain\Repository\TaskRepository')]
class Task extends AbstractEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'Ramsey\Uuid\Doctrine\UuidGenerator')]
    protected \Ramsey\Uuid\UuidInterface $uuid;

    public function getUuid(): \Ramsey\Uuid\UuidInterface
    {
        return $this->uuid;
    }

    #[ORM\Column(type: 'string', length: 255, options: ['default' => ''])]
    protected string $title = '';

    /**
     * @return $this
     */
    public function setTitle(string $title)
    {
        if ($this->checkStrLenMax($title, 255)) {
            $this->title = $title;
        }

        return $this;
    }

    public function getTitle(): string
    {
        if ($this->title) {
            return $this->title;
        }

        $action = explode('\\', $this->action);

        return end($action);
    }

    #[ORM\Column(type: 'string', options: ['default' => ''])]
    protected string $action = '';

    /**
     * @return $this
     */
    public function setAction(string $action)
    {
        $this->action = $action;

        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    #[ORM\Column(type: 'float', scale: 2, precision: 10, options: ['default' => 0])]
    protected float $progress = .00;

    /**
     * @return $this
     */
    public function setProgress(float $progress)
    {
        $this->progress = $progress;

        return $this;
    }

    public function getProgress(): float
    {
        return $this->progress;
    }

    /**
     * @see \App\Domain\Types\TaskStatusType::LIST
     */
    #[ORM\Column(type: 'TaskStatusType', options: ['default' => \App\Domain\Types\TaskStatusType::STATUS_QUEUE])]
    public string $status = \App\Domain\Types\TaskStatusType::STATUS_QUEUE;

    /**
     * @return $this
     */
    public function setStatus(string $status)
    {
        if (in_array($status, \App\Domain\Types\TaskStatusType::LIST, true)) {
            $this->status = $status;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    #[ORM\Column(type: 'json', options: ['default' => '{}'])]
    protected array $params = [];

    /**
     * @return $this
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    #[ORM\Column(type: 'string', length: 1000, options: ['default' => ''])]
    protected string $output = '';

    /**
     * @return $this
     */
    public function setOutput(string $output)
    {
        if ($this->checkStrLenMax($output, 1000)) {
            $this->output = $output;
        }

        return $this;
    }

    public function getOutput(): string
    {
        return $this->output;
    }

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected \DateTime $date;

    /**
     * @param mixed $timezone
     * @param mixed $date
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function setDate($date, $timezone = 'UTC')
    {
        $this->date = $this->getDateTimeByValue($date, $timezone);

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
