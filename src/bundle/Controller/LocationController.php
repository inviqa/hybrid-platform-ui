<?php

/**
 * File containing the LocationController class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\HybridPlatformUiBundle\Controller;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use EzSystems\HybridPlatformUi\Form\UiFormFactory;
use EzSystems\HybridPlatformUi\Repository\UiLocationService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class LocationController extends TabController
{
    /**
     * @var UiLocationService
     */
    private $uiLocationService;

    /**
     * @var UiFormFactory
     */
    private $formFactory;

    public function __construct(
        UiLocationService $uiLocationService,
        RouterInterface $router,
        ContentService $contentService,
        UiFormFactory $formFactory
    ) {
        $this->uiLocationService = $uiLocationService;
        $this->formFactory = $formFactory;
        parent::__construct($router, $contentService);
    }

    public function contentViewTabAction(
        ContentView $view
    ) {
        $versionInfo = $view->getContent()->getVersionInfo();
        $contentInfo = $versionInfo->getContentInfo();

        if ($contentInfo->published) {
            $locations = $this->uiLocationService->loadLocations($contentInfo);
            $actionsForm = $this->formFactory->createLocationsActionForm($locations);
            $swapLocationsForm = $this->formFactory->createLocationsContentSwapForm();
            $visibilityForm = $this->formFactory->createLocationVisibilityForm();

            $view->addParameters([
                'locations' => $locations,
                'actionsForm' => $actionsForm->createView(),
                'swapLocationsForm' => $swapLocationsForm->createView(),
                'visibilityForm' => $visibilityForm->createView(),
            ]);
        }

        return $view;
    }

    public function actionsAction(
        Content $content,
        Request $request
    ) {
        $actionsForm = $this->formFactory->createLocationsActionForm();
        $actionsForm->handleRequest($request);

        $redirectLocationId = $request->query->get('redirectLocationId', $content->contentInfo->mainLocationId);

        if ($actionsForm->isValid()) {
            $resetLocation = $this->deleteLocationsBasedOnFormSubmit($actionsForm, $redirectLocationId);

            if ($resetLocation) {
                return $this->resetToMainLocation($content->id);
            }

            $this->addLocationBasedOnFormSubmit($actionsForm, $content);
        }

        return $this->reloadTab('locations', $content->id, $redirectLocationId);
    }

    public function swapLocationAction(
        Content $content,
        Location $location,
        Request $request
    ) {
        $swapLocationsForm = $this->formFactory->createLocationsContentSwapForm();
        $swapLocationsForm->handleRequest($request);

        if ($swapLocationsForm->isValid()) {
            $newLocationId = $swapLocationsForm->get('new_location_id')->getData();
            $location = $this->uiLocationService->swapLocations($location, $newLocationId);

            return $this->resetLocation($location);
        }

        $redirectLocationId = $request->query->get('redirectLocationId', $content->contentInfo->mainLocationId);

        return $this->reloadTab('locations', $content->id, $redirectLocationId);
    }

    public function updateDefaultSortOrderAction(
        Location $location,
        Content $content,
        Request $request,
        LocationService $locationService,
        UiFormFactory $formFactory
    ) {
        $form = $formFactory->createLocationOrderingForm($location);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $locationService->updateLocation($location, $form->getData());
        }

        return $this->reloadTab('details', $content->id, $location->id);
    }

    public function visibilityAction(
        Request $request,
        LocationService $locationService,
        UiFormFactory $formFactory,
        TranslatorInterface $translator
    ) {
        try {
            $visibilityForm = $formFactory->createLocationVisibilityForm();
            $visibilityForm->handleRequest($request);

            if (!$visibilityForm->isValid()) {
                /** @Desc("Invalid form submission.") */
                $message = $translator->trans(
                    'locationview.locations.notification.invalid.form', [], 'locationview'
                );

                return;
            }

            $visibility = $visibilityForm->get('visibility')->getData();
            $locationId = $visibilityForm->get('locationId')->getData();

            $location = $locationService->loadLocation($locationId);

            if ($visibility) {
                $locationService->unhideLocation($location);
                /** @Desc("The Location #%id% is now visible") */
                $message = $translator->trans(
                    'locationview.locations.notification.visible', ['%id%' => $location->id], 'locationview'
                );

                return;
            }

            $locationService->hideLocation($location);
            /** @Desc("The Location #%id% is now hidden") */
            $message = $translator->trans(
                'locationview.locations.notification.hidden', ['%id%' => $location->id], 'locationview'
            );

            return;
        } catch (\Exception $e) {
            /** @Desc("Error updating location visibility") */
            $message = $translator->trans('locationview.locations.visibility.error', [], 'locationview');

            return;
        }
    }

    /**
     * Delete locations based on form submit.
     *
     * @param FormInterface $form
     * @param mixed $redirectLocationId
     *
     * @return bool Whether to reset location or not.
     */
    private function deleteLocationsBasedOnFormSubmit(FormInterface $form, $redirectLocationId)
    {
        $locationIds = array_keys($form->get('removeLocations')->getData());

        if ($form->get('delete')->isClicked()) {
            $this->uiLocationService->deleteLocations($locationIds);

            if (in_array($redirectLocationId, $locationIds)) {
                return true;
            }
        }

        return false;
    }

    private function addLocationBasedOnFormSubmit(FormInterface $form, Content $content)
    {
        if ($form->get('add')->isClicked()) {
            $this->uiLocationService->addLocation(
                $content->contentInfo,
                $form->get('parentLocationId')->getData()
            );
        }
    }
}
