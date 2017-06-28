<?php

/**
 * File containing the LocationController class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\HybridPlatformUiBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use EzSystems\HybridPlatformUi\Repository\UiLocationService;
use EzSystems\HybridPlatformUiBundle\Form\Locations\Actions;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class LocationController extends Controller
{
    public function actionsAction(Request $request, UiLocationService $uiLocationService, RouterInterface $router)
    {
        $actionsForm = $this->createForm(Actions::class);
        $actionsForm->handleRequest($request);

        if ($actionsForm->isValid()) {
            $locationIds = array_keys($actionsForm->get('locationIds')->getData());
            $contentId = (int) $actionsForm->get('contentId')->getData();

            if ($actionsForm->get('delete')->isClicked()) {
                $uiLocationService->deleteLocations($locationIds, $contentId);
            }
        }

        return new RedirectResponse(
            $router->generate(
                '_ez_content_view',
                [
                    'contentId' => $contentId,
                    'viewType' => 'locations_tab',
                ]
            )
        );
    }
}
