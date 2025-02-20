<?php declare(strict_types=1);

namespace App\Application\Actions\Cup\User\Group;

use App\Application\Actions\Cup\User\UserAction;
use App\Domain\Service\User\Exception\TitleAlreadyExistsException;

class UpdateAction extends UserAction
{
    protected function action(): \Slim\Psr7\Response
    {
        if ($this->resolveArg('uuid')) {
            $userGroup = $this->userGroupService->read(['uuid' => $this->resolveArg('uuid')]);

            if ($userGroup) {
                if ($this->isPost()) {
                    try {
                        $userGroup = $this->userGroupService->update($userGroup, [
                            'title' => $this->getParam('title'),
                            'description' => $this->getParam('description'),
                            'access' => $this->getParam('access', []),
                        ]);

                        $this->container->get(\App\Application\PubSub::class)->publish('cup:user:group:edit', $userGroup);

                        switch (true) {
                            case $this->getParam('save', 'exit') === 'exit':
                                return $this->respondWithRedirect('/cup/user/group');

                            default:
                                return $this->respondWithRedirect('/cup/user/group/' . $userGroup->getUuid() . '/edit');
                        }
                    } catch (TitleAlreadyExistsException $e) {
                        $this->addError('title', $e->getMessage());
                    }
                }

                return $this->respondWithTemplate('cup/user/group/form.twig', [
                    'item' => $userGroup,
                    'routes' => [
                        'all' => $this->getRoutes()->all(),
                    ],
                ]);
            }
        }

        return $this->respondWithRedirect('/cup/user/group');
    }
}
