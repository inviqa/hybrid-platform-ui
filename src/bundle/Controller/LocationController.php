<?php

/**
 * File containing the LocationController class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\HybridPlatformUiBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\HybridPlatformUiBundle\Form\Location\Ordering;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class LocationController extends Controller
{
    public function updateSubItemOrderAction(
        Location $location,
        Content $content,
        Request $request,
        LocationService $locationService,
        RouterInterface $router
    ) {
        $form = $this->createForm(Ordering::class, [], ['current_sort_field' => $location->sortField]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $updateStruct = $locationService->newLocationUpdateStruct();

            $updateStruct->sortField = $form->get('sortField')->getData();
            $updateStruct->sortOrder = $form->get('sortOrder')->getData();

            $locationService->updateLocation($location, $updateStruct);
        }

        return new RedirectResponse(
            $router->generate(
                '_ez_content_view',
                [
                    'contentId' => $content->id,
                    'locationId' => $location->id,
                    'layout' => 'true',
                    'viewType' => 'full',
                ]
            )
        );
    }
}
