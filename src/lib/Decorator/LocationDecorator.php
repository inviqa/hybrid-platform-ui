<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\HybridPlatformUi\Decorator;

use eZ\Publish\API\Repository\Values\Content\Location;

class LocationDecorator extends ValueObjectDecorator
{
    /**
     * @var int
     */
    public $childCount;

    /**
     * @var Location
     */
    protected $location;

    public function __construct(Location $location)
    {
        $this->location = $location;
    }

    /**
     * @return Location
     */
    public function getValueObject()
    {
        return $this->location;
    }

    public function isDraft()
    {
        return $this->location->isDraft();
    }

    public function getSortClauses()
    {
        return $this->location->getSortClauses();
    }

    public function getContentInfo()
    {
        return $this->location->getContentInfo();
    }
}
