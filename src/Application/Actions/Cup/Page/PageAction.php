<?php declare(strict_types=1);

namespace App\Application\Actions\Cup\Page;

use App\Domain\AbstractAction;
use App\Domain\Service\Page\PageService;
use Psr\Container\ContainerInterface;

abstract class PageAction extends AbstractAction
{
    protected PageService $pageService;

    /**
     * {@inheritdoc}
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->pageService = $container->get(PageService::class);
    }
}
