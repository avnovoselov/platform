<?php declare(strict_types=1);

namespace App\Domain\Entities\User;

use App\Domain\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'user_subscriber')]
#[ORM\Entity(repositoryClass: 'App\Domain\Repository\User\SubscriberRepository')]
class Subscriber extends AbstractEntity
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

    #[ORM\Column(type: 'string', length: 120, unique: true, options: ['default' => ''])]
    protected string $email = '';

    /**
     * @throws \App\Domain\Service\User\Exception\WrongEmailValueException
     *
     * @return $this
     */
    public function setEmail(string $email)
    {
        try {
            if ($this->checkStrLenMax($email, 120) && $this->checkEmailByValue($email)) {
                $this->email = $email;
            }
        } catch (\RuntimeException) {
            throw new \App\Domain\Service\User\Exception\WrongEmailValueException();
        }

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
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
