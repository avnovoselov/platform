<?php

namespace App\Domain\Filters\Traits;

use Slim\App;

trait PublicationFilterRules
{
    /**
     * Проверяет уникальность адреса публикации
     *
     * @return \Closure
     */
    public function UniquePublicationAddress()
    {
        return function (&$data, $field) {
            /** @var App $app */
            $app = $GLOBALS['app'];

            /** @var \Doctrine\Common\Persistence\ObjectRepository|\Doctrine\ORM\EntityRepository $publicationRepository */
            $publicationRepository = $app->getContainer()->get(\Doctrine\ORM\EntityManager::class)->getRepository(\App\Domain\Entities\Publication::class);

            /** @var \App\Domain\Entities\Publication $publication */
            $publication = $publicationRepository->findOneBy(['address' => str_escape($data[$field])]);

            return $publication === null || (!empty($data['uuid']) && $publication->uuid === $data['uuid']);
        };
    }

    /**
     * Проверяет опрос публикации
     *
     * @return \Closure
     */
    public function ValidPublicationPoll()
    {
        return function (&$data, $field) {
            $buf = [
                'question' => '',
                'answer' => '',
            ];
            $value = &$data[$field];

            if (!is_array($value)) {
                $value = $buf;

                return true;
            }

            if (isset($value['question'])) {
                $buf['question'] = $value['question'];
            }
            if (isset($value['answer'])) {
                $buf['answer'] = $value['answer'];
            }

            $value = $buf;

            return true;
        };
    }

    /**
     * Проверяет содержимое публикаций
     *
     * @return \Closure
     */
    public function ValidPublicationContent()
    {
        return function (&$data, $field) {
            $buf = [
                'short' => '',
                'full' => '',
            ];
            $value = &$data[$field];

            if (!is_array($value)) {
                $value = $buf;

                return true;
            }

            if (isset($value['short'])) {
                $buf['short'] = $value['short'];
            }
            if (isset($value['full'])) {
                $buf['full'] = $value['full'];
            }
            if (!$buf['full'] && $buf['short']) {
                $buf['full'] = $buf['short'];
            }

            $value = $buf;

            return true;
        };
    }

    /**
     * Проверяет уникальность адреса категории
     *
     * @return \Closure
     */
    public function UniqueCategoryAddress()
    {
        return function (&$data, $field) {
            /** @var App $app */
            $app = $GLOBALS['app'];

            /** @var \Doctrine\Common\Persistence\ObjectRepository|\Doctrine\ORM\EntityRepository $categoryRepository */
            $categoryRepository = $app->getContainer()->get(\Doctrine\ORM\EntityManager::class)->getRepository(\App\Domain\Entities\Publication\Category::class);

            /** @var \App\Domain\Entities\Publication\Category $category */
            $category = $categoryRepository->findOneBy(['address' => str_escape($data[$field])]);

            return $category === null || (!empty($data['uuid']) && $category->uuid === $data['uuid']);
        };
    }

    /**
     * Проверяет поле sort
     *
     * @return \Closure
     */
    public function ValidCategorySort()
    {
        return function (&$data, $field) {
            $buf = [
                'by' => \App\Domain\References\Publication::ORDER_BY_DATE,
                'direction' => \App\Domain\References\Publication::ORDER_DIRECTION_ASC,
            ];
            $value = &$data[$field];

            if (!is_array($value)) {
                $value = $buf;

                return true;
            }

            if (
                isset($value['by']) &&
                in_array($value['by'], \App\Domain\References\Publication::ORDER_BY)
            ) {
                $buf['by'] = $value['by'];
            }
            if (
                isset($value['direction']) &&
                in_array($value['direction'], \App\Domain\References\Publication::ORDER_DIRECTION)
            ) {
                $buf['direction'] = $value['direction'];
            }

            $value = $buf;

            return true;
        };
    }

    /**
     * Проверяет настройки шаблонов категории публикаций
     *
     * @return \Closure
     */
    public function ValidCategoryTemplate()
    {
        return function (&$data, $field) {
            $buf = [
                'list' => '',
                'short' => '',
                'full' => '',
            ];
            $value = &$data[$field];

            if (!is_array($value)) {
                $value = $buf;

                return true;
            }

            if (isset($value['list'])) {
                $buf['list'] = $value['list'];
            }
            if (isset($value['short'])) {
                $buf['short'] = $value['short'];
            }
            if (isset($value['full'])) {
                $buf['full'] = $value['full'];
            }

            $value = $buf;

            return true;
        };
    }
}
