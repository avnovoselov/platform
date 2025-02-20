<?php declare(strict_types=1);

namespace App\Application\Actions\Cup\GuestBook;

use App\Domain\AbstractAction;
use App\Domain\Service\GuestBook\GuestBookService;
use Psr\Container\ContainerInterface;

abstract class GuestBookAction extends AbstractAction
{
    protected GuestBookService $guestBookService;

    /**
     * {@inheritdoc}
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->guestBookService = $container->get(GuestBookService::class);
    }
}
