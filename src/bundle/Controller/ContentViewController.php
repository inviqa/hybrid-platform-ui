<?php

/**
 * File containing the ContentViewController class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\HybridPlatformUiBundle\Controller;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\Controller\Controller;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use EzSystems\HybridPlatformUi\Decorator\LocationDecorator;

class ContentViewController extends Controller
{
    public function locationsTabAction(ContentView $view)
    {
        $versionInfo = $view->getContent()->getVersionInfo();
        $contentInfo = $versionInfo->getContentInfo();

        if ($contentInfo->published) {
            $locationRepository = $this->getRepository()->getLocationService();
            $locations = $locationRepository->loadLocations($contentInfo);

            $locations = array_map(function (Location $location) {
                return new LocationDecorator($location);
            }, $locations);

            foreach ($locations as $location) {
                $location->childCount = $locationRepository->getLocationChildCount($location->getValueObject());
            }

            $view->addParameters([
                'locations' => $locations
            ]);
        }

        return $view;
    }
}
