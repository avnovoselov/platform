<?php declare(strict_types=1);

namespace App\Domain\Service\Parameter;

use App\Domain\AbstractService;
use App\Domain\Entities\Parameter;
use App\Domain\Repository\ParameterRepository;
use App\Domain\Service\Parameter\Exception\ParameterAlreadyExistsException;
use App\Domain\Service\Parameter\Exception\ParameterNotFoundException;
use Illuminate\Support\Collection;

class ParameterService extends AbstractService
{
    /**
     * @var ParameterRepository
     */
    protected mixed $service;

    protected function init(): void
    {
        $this->service = $this->entityManager->getRepository(Parameter::class);
    }

    /**
     * @throws ParameterAlreadyExistsException
     */
    public function create(array $data = []): Parameter
    {
        $default = [
            'key' => '',
            'value' => '',
        ];
        $data = array_merge($default, $data);

        if ($data['key'] && $this->service->findOneByKey($data['key']) !== null) {
            throw new ParameterAlreadyExistsException();
        }

        $parameter = (new Parameter())
            ->setKey($data['key'])
            ->setValue($data['value']);

        $this->entityManager->persist($parameter);
        $this->entityManager->flush();

        return $parameter;
    }

    public function read(array $data = [], mixed $fallback = null): Collection|Parameter|null
    {
        $default = [
            'key' => null,
        ];
        $data = array_merge($default, static::$default_read, $data);

        $criteria = [];

        if ($data['key'] !== null) {
            $criteria['key'] = $data['key'];
        }

        try {
            switch (true) {
                case !is_array($data['key']) && $data['key'] !== null:
                    $parameter = $this->service->findOneByKey((string) $data['key']);

                    if (empty($parameter)) {
                        $parameter = (new Parameter())->setKey($data['key'])->setValue($fallback);
                        $this->entityManager->persist($parameter);
                    }

                    return $parameter;

                default:
                    return collect($this->service->findBy($criteria, $data['order'], $data['limit'], $data['offset']));
            }
        } catch (\Doctrine\DBAL\Exception\TableNotFoundException) {
            if ($fallback) {
                return (new Parameter())->setKey($data['key'])->setValue($fallback);
            }

            return null;
        }
    }

    /**
     * @param Parameter|string $entity
     */
    public function update($entity, array $data = []): Parameter
    {
        switch (true) {
            case is_string($entity):
                $entity = $this->service->findOneByKey((string) $entity);

                break;
        }

        if (is_object($entity) && is_a($entity, Parameter::class)) {
            $default = [
                'key' => null,
                'value' => null,
            ];
            $data = array_merge($default, $data);

            if ($data !== $default) {
                if ($data['key'] !== null) {
                    $found = $this->service->findOneByKey($data['key']);

                    if ($found === null || $found === $entity) {
                        $entity->setKey($data['key']);
                    } else {
                        throw new ParameterAlreadyExistsException();
                    }
                }
                if ($data['value'] !== null) {
                    $entity->setValue($data['value']);
                }

                $this->entityManager->flush();
            }

            return $entity;
        }

        throw new ParameterNotFoundException();
    }

    /**
     * @param Parameter|string $entity
     *
     * @throws ParameterNotFoundException
     */
    public function delete($entity): bool
    {
        switch (true) {
            case is_string($entity):
                $entity = $this->service->findOneByKey((string) $entity);

                break;
        }

        if (is_object($entity) && is_a($entity, Parameter::class)) {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();

            return true;
        }

        throw new ParameterNotFoundException();
    }
}
