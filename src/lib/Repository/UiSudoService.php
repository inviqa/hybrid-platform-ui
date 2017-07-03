<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\HybridPlatformUi\Repository;

use eZ\Publish\API\Repository\Repository;

/**
 * Service for loading translations.
 */
class UiSudoService
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var UiUserService
     */
    private $uiUserService;

    public function __construct(Repository $repository, UiUserService $uiUserService)
    {
        $this->repository = $repository;
        $this->uiUserService = $uiUserService;
    }

    /**
     * Loads a user ignoring privileges.
     *
     * @param int $userId
     *
     * @return \eZ\Publish\API\Repository\Values\User\User
     */
    public function loadUser(int $userId)
    {
        return $this->repository->sudo(function () use ($userId) {
            return $this->uiUserService->loadUser($userId);
        });
    }
}
