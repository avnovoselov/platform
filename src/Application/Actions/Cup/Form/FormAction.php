<?php declare(strict_types=1);

namespace App\Application\Actions\Cup\Form;

use App\Domain\AbstractAction;
use App\Domain\Service\Form\DataService as FormDataService;
use App\Domain\Service\Form\FormService;
use Psr\Container\ContainerInterface;

abstract class FormAction extends AbstractAction
{
    /**
     * @var FormService
     */
    protected FormService $formService;

    /**
     * @var FormDataService
     */
    protected FormDataService $formDataService;

    /**
     * {@inheritdoc}
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->formService = FormService::getWithContainer($container);
        $this->formDataService = FormDataService::getWithContainer($container);
    }
}
