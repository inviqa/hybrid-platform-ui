<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\HybridPlatformUi\Repository;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\UserService;

/**
 * Service for loading user.
 */
class UiUserService
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Loads user and handles not found exception.
     *
     * @param int $userId
     *
     * @return \eZ\Publish\API\Repository\Values\User\User|null
     */
    public function loadUser(int $userId)
    {
        try {
            return $this->userService->loadUser($userId);
        } catch (NotFoundException $e) {
            return null;
        }
    }
}
