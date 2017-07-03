<?php

/**
 * File containing the LocationController class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\HybridPlatformUiBundle\Controller;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\Controller\Controller;
use eZ\Publish\API\Repository\Values\Content\Content;
use EzSystems\HybridPlatformUi\Builder\NotificationBuilder;
use EzSystems\HybridPlatformUi\Form\UiFormFactory;
use EzSystems\HybridPlatformUi\Repository\UiLocationService;
use EzSystems\HybridPlatformUi\Response\NotificationResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class LocationController extends Controller
{
    public function actionsAction(
        Content $content,
        Request $request,
        UiLocationService $uiLocationService,
        RouterInterface $router,
        UiFormFactory $formFactory
    ) {
        $actionsForm = $formFactory->createLocationsActionForm();
        $actionsForm->handleRequest($request);

        if ($actionsForm->isValid()) {
            $locationIds = array_keys($actionsForm->get('removeLocations')->getData());

            if ($actionsForm->get('delete')->isClicked()) {
                $uiLocationService->deleteLocations($locationIds, $content->id);
            }
        }

        return new RedirectResponse(
            $router->generate(
                '_ez_content_view',
                [
                    'contentId' => $content->id,
                ]
            )
        );
    }

    public function visibilityAction(
        Request $request,
        LocationService $locationService,
        UiFormFactory $formFactory,
        TranslatorInterface $translator,
        NotificationBuilder $notificationBuilder
    ) {
        try {
            $visibilityForm = $formFactory->createLocationsVisibilityForm();
            $visibilityForm->handleRequest($request);

            if (!$visibilityForm->isValid()) {
                throw new \Exception('Invalid form submission.');
            }

            $visibility = $visibilityForm->get('visibility')->getData();
            $locationId = $visibilityForm->get('locationId')->getData();

            $location = $locationService->loadLocation($locationId);

            if ($visibility) {
                $locationService->unhideLocation($location);
                /** @Desc("The Location #%id% is now visible") */
                $message = $translator->trans(
                    'locationview.locations.notification.visible', ['id' => $location->id], 'locationview'
                );

                return $this->success($message, $notificationBuilder);
            }

            $locationService->hideLocation($location);
            /** @Desc("The Location #%id% is now hidden") */
            $message = $translator->trans(
                'locationview.locations.notification.hidden', ['id' => $location->id], 'locationview'
            );

            return $this->success($message, $notificationBuilder);
        } catch (\Exception $e) {
            /** @Desc("Error updating location visibility") */
            $message = $translator->trans('locationview.locations.visibility.error', [], 'locationview');

            return $this->error($message, $e->getMessage(), $notificationBuilder);
        }
    }

    private function success(string $message, NotificationBuilder $notificationBuilder)
    {
        $notification = $notificationBuilder
            ->setMessage($message)
            ->setSuccess()
            ->getResult();

        return NotificationResponse::success($notification);
    }

    private function error(string $message, string $errorDetails, NotificationBuilder $notificationBuilder)
    {
        $notification = $notificationBuilder
            ->setMessage($message)
            ->setError()
            ->setErrorDetails($errorDetails)
            ->getResult();

        return NotificationResponse::error($notification);
    }
}
