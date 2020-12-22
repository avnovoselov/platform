<?php declare(strict_types=1);

namespace App\Application\Actions\Cup\Catalog\Order;

use App\Application\Actions\Cup\Catalog\CatalogAction;
use App\Domain\Service\User\Exception\UserNotFoundException;

class OrderUpdateAction extends CatalogAction
{
    protected function action(): \Slim\Http\Response
    {
        if ($this->resolveArg('order') && \Ramsey\Uuid\Uuid::isValid($this->resolveArg('order'))) {
            $order = $this->catalogOrderService->read(['uuid' => $this->resolveArg('order')]);

            if ($order) {
                if ($this->request->isPost()) {
                    $order = $this->catalogOrderService->update($order, [
                        'serial' => $order->serial,
                        'delivery' => $this->request->getParam('delivery'),
                        'user_uuid' => $this->request->getParam('user_uuid'),
                        'list' => (array) $this->request->getParam('list', []),
                        'phone' => $this->request->getParam('phone'),
                        'email' => $this->request->getParam('email'),
                        'status' => $this->request->getParam('status'),
                        'comment' => $this->request->getParam('comment'),
                        'shipping' => $this->request->getParam('shipping'),
                        'external_id' => $this->request->getParam('external_id'),
                    ]);

                    switch (true) {
                        case $this->request->getParam('save', 'exit') === 'exit':
                            return $this->response->withRedirect('/cup/catalog/order');
                        default:
                            return $this->response->withRedirect('/cup/catalog/order/' . $order->getUuid() . '/edit');
                    }
                }

                $products = $this->catalogProductService->read(['uuid' => array_keys($order->getList())]);

                try {
                    $user = $this->userService->read(['uuid' => $order->getUserUuid()]);
                } catch (UserNotFoundException $e) {
                    $user = null;
                }

                return $this->respondWithTemplate('cup/catalog/order/form.twig', [
                    'order' => $order,
                    'products' => $products,
                    'item' => $user,
                ]);
            }
        }

        return $this->response->withRedirect('/cup/catalog/order');
    }
}
