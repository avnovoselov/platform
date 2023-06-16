<?php declare(strict_types=1);

namespace App\Domain\Service\User;

use App\Domain\AbstractService;
use App\Domain\Entities\User\Subscriber as UserSubscriber;
use App\Domain\Repository\User\SubscriberRepository as UserSubscriberRepository;
use App\Domain\Service\User\Exception\EmailAlreadyExistsException;
use App\Domain\Service\User\Exception\MissingUniqueValueException;
use App\Domain\Service\User\Exception\UserNotFoundException;
use App\Domain\Service\User\Exception\WrongEmailValueException;
use Illuminate\Support\Collection;
use Ramsey\Uuid\UuidInterface as Uuid;

class SubscriberService extends AbstractService
{
    /**
     * @var UserSubscriberRepository
     */
    protected mixed $service;

    protected function init(): void
    {
        $this->service = $this->entityManager->getRepository(UserSubscriber::class);
    }

    /**
     * @throws EmailAlreadyExistsException
     * @throws WrongEmailValueException
     */
    public function create(array $data = []): UserSubscriber
    {
        $default = [
            'email' => '',
            'date' => 'now',
        ];
        $data = array_merge($default, $data);

        if ($data['email'] && $this->service->findOneByEmail($data['email']) !== null) {
            throw new EmailAlreadyExistsException();
        }
        if (!$data['email']) {
            throw new MissingUniqueValueException();
        }

        $userSubscriber = (new UserSubscriber())
            ->setEmail($data['email'])
            ->setDate($data['date'], $this->parameter('common_timezone', 'UTC'));

        $this->entityManager->persist($userSubscriber);
        $this->entityManager->flush();

        return $userSubscriber;
    }

    /**
     * @throws UserNotFoundException
     *
     * @return Collection|UserSubscriber
     */
    public function read(array $data = [])
    {
        $default = [
            'uuid' => null,
            'email' => null,
            'date' => null,
        ];
        $data = array_merge($default, static::$default_read, $data);

        $criteria = [];

        if ($data['uuid'] !== null) {
            $criteria['uuid'] = $data['uuid'];
        }
        if ($data['email'] !== null) {
            $criteria['email'] = $data['email'];
        }
        if ($data['date'] !== null) {
            $criteria['date'] = $data['date'];
        }

        try {
            switch (true) {
                case !is_array($data['uuid']) && $data['uuid'] !== null:
                case !is_array($data['email']) && $data['email'] !== null:
                    $userSubscriber = $this->service->findOneBy($criteria);

                    if (empty($userSubscriber)) {
                        throw new UserNotFoundException();
                    }

                    return $userSubscriber;

                default:
                    return collect($this->service->findBy($criteria, $data['order'], $data['limit'], $data['offset']));
            }
        } catch (\Doctrine\DBAL\Exception\TableNotFoundException) {
            return null;
        }
    }

    /**
     * @param string|UserSubscriber|Uuid $entity
     *
     * @throws EmailAlreadyExistsException
     * @throws UserNotFoundException
     * @throws WrongEmailValueException
     *
     * @return UserSubscriber
     */
    public function update($entity, array $data = [])
    {
        switch (true) {
            case is_string($entity) && \Ramsey\Uuid\Uuid::isValid($entity):
            case is_object($entity) && is_a($entity, Uuid::class):
                $entity = $this->service->findOneByUuid((string) $entity);

                break;
        }

        if (is_object($entity) && is_a($entity, UserSubscriber::class)) {
            $default = [
                'email' => null,
            ];
            $data = array_merge($default, $data);

            if ($data !== $default) {
                if ($data['email'] !== null) {
                    $found = $this->service->findOneByEmail($data['email']);

                    if ($found === null || $found === $entity) {
                        $entity->setEmail($data['email']);
                    } else {
                        throw new EmailAlreadyExistsException();
                    }
                }

                $entity->setDate('now', $this->parameter('common_timezone', 'UTC'));

                $this->entityManager->flush();
            }

            return $entity;
        }

        throw new UserNotFoundException();
    }

    /**
     * @param mixed $entity
     *
     * @throws UserNotFoundException
     */
    public function delete($entity): bool
    {
        switch (true) {
            case is_string($entity) && \Ramsey\Uuid\Uuid::isValid($entity):
            case is_object($entity) && is_a($entity, Uuid::class):
                $entity = $this->service->findOneByUuid((string) $entity);

                break;
        }

        if (is_object($entity) && is_a($entity, UserSubscriber::class)) {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();

            return true;
        }

        throw new UserNotFoundException();
    }
}
