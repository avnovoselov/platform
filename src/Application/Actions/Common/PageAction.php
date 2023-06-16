<?php declare(strict_types=1);

namespace App\Application\Actions\Common;

use App\Domain\AbstractAction;
use App\Domain\Exceptions\HttpBadRequestException;
use App\Domain\Service\Page\Exception\PageNotFoundException;
use App\Domain\Service\Page\PageService;

class PageAction extends AbstractAction
{
    protected function action(): \Slim\Psr7\Response
    {
        $pageService = $this->container->get(PageService::class);

        try {
            $page = $pageService->read(['address' => ltrim($this->resolveArg('args'), '/')]);

            return $this->respond($page->getTemplate(), [
                'page' => $page,
            ]);
        } catch (HttpBadRequestException) {
            return $this->respond('p400.twig')->withStatus(400);
        } catch (PageNotFoundException) {
            return $this->respond('p404.twig')->withStatus(404);
        }
    }
}
