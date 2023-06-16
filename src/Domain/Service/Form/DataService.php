<?php declare(strict_types=1);

namespace App\Domain\Service\Form;

use App\Domain\AbstractService;
use App\Domain\Entities\Form\Data as FromData;
use App\Domain\Repository\Form\DataRepository as FormDataRepository;
use App\Domain\Service\Form\Exception\FormDataNotFoundException;
use App\Domain\Service\Form\Exception\MissingMessageValueException;
use Illuminate\Support\Collection;
use Ramsey\Uuid\UuidInterface as Uuid;

class DataService extends AbstractService
{
    /**
     * @var FormDataRepository
     */
    protected mixed $service;

    protected function init(): void
    {
        $this->service = $this->entityManager->getRepository(FromData::class);
    }

    /**
     * @throws MissingMessageValueException
     */
    public function create(array $data = []): FromData
    {
        $default = [
            'form_uuid' => \Ramsey\Uuid\Uuid::NIL,
            'data' => [],
            'message' => '',
            'date' => 'now',
        ];
        $data = array_merge($default, $data);

        if (!$data['message'] && !$data['data']) {
            throw new MissingMessageValueException();
        }

        $form = (new FromData())
            ->setFormUuid($data['form_uuid'])
            ->setData($data['data'])
            ->setMessage($data['message'])
            ->setDate($data['date'], $this->parameter('common_timezone', 'UTC'));

        $this->entityManager->persist($form);
        $this->entityManager->flush();

        return $form;
    }

    /**
     * @throws FormDataNotFoundException
     *
     * @return Collection|FromData
     */
    public function read(array $data = [])
    {
        $default = [
            'uuid' => null,
            'form_uuid' => null,
        ];
        $data = array_merge($default, static::$default_read, $data);

        $criteria = [];

        if ($data['uuid'] !== null) {
            $criteria['uuid'] = $data['uuid'];
        }
        if ($data['form_uuid'] !== null) {
            $criteria['form_uuid'] = $data['form_uuid'];
        }

        try {
            switch (true) {
                case !is_array($data['uuid']) && $data['uuid'] !== null:
                    $formData = $this->service->findOneBy($criteria);

                    if (empty($formData)) {
                        throw new FormDataNotFoundException();
                    }

                    return $formData;

                default:
                    return collect($this->service->findBy($criteria, $data['order'], $data['limit'], $data['offset']));
            }
        } catch (\Doctrine\DBAL\Exception\TableNotFoundException) {
            return null;
        }
    }

    /**
     * @param FromData|string|Uuid $entity
     *
     * @throws FormDataNotFoundException
     */
    public function update($entity, array $data = []): FromData
    {
        switch (true) {
            case is_string($entity) && \Ramsey\Uuid\Uuid::isValid($entity):
            case is_object($entity) && is_a($entity, Uuid::class):
                $entity = $this->service->findOneByUuid((string) $entity);

                break;
        }

        if (is_object($entity) && is_a($entity, FromData::class)) {
            $default = [
                'form_uuid' => null,
                'data' => null,
                'message' => null,
                'date' => null,
            ];
            $data = array_merge($default, $data);

            if ($data !== $default) {
                if ($data['form_uuid'] !== null) {
                    $entity->setFormUuid($data['form_uuid']);
                }
                if ($data['data'] !== null) {
                    $entity->setData($data['data']);
                }
                if ($data['message'] !== null) {
                    $entity->setMessage($data['message']);
                }
                if ($data['date'] !== null) {
                    $entity->setDate($data['date'], $this->parameter('common_timezone', 'UTC'));
                }

                $this->entityManager->flush();
            }

            return $entity;
        }

        throw new FormDataNotFoundException();
    }

    /**
     * @param FromData|string|Uuid $entity
     *
     * @throws FormDataNotFoundException
     */
    public function delete($entity): bool
    {
        switch (true) {
            case is_string($entity) && \Ramsey\Uuid\Uuid::isValid($entity):
            case is_object($entity) && is_a($entity, Uuid::class):
                $entity = $this->service->findOneByUuid((string) $entity);

                break;
        }

        if (is_object($entity) && is_a($entity, FromData::class)) {
            if (($files = $entity->getFiles()) && $files->isNotEmpty()) {
                $fileService = $this->container->get(\App\Domain\Service\File\FileService::class);

                /**
                 * @var \App\Domain\Entities\File $file
                 */
                foreach ($files as $file) {
                    try {
                        $fileService->delete($file);
                    } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException) {
                        // nothing, file not found
                    } catch (\App\Domain\Service\File\Exception\FileNotFoundException) {
                        // nothing, file not found
                    }
                }
            }

            $this->entityManager->remove($entity);
            $this->entityManager->flush();

            return true;
        }

        throw new FormDataNotFoundException();
    }
}
