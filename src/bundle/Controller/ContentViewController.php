<?php

/**
 * File containing the ContentViewController class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\HybridPlatformUiBundle\Controller;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use EzSystems\HybridPlatformUi\Filter\VersionFilter;
use EzSystems\HybridPlatformUi\Mapper\Form\Location\OrderingMapper;
use EzSystems\HybridPlatformUi\Repository\UiFieldGroupService;
use EzSystems\HybridPlatformUi\Repository\UiLocationService;
use EzSystems\HybridPlatformUiBundle\Form\Location\Ordering;

class ContentViewController extends Controller
{
    public function contentTabAction(ContentView $view, UiFieldGroupService $fieldGroupService)
    {
        $versionInfo = $view->getContent()->getVersionInfo();

        $view->addParameters([
            'fieldGroups' => $fieldGroupService->loadFieldGroups($versionInfo->getContentInfo()),
        ]);

        return $view;
    }

    public function detailsTabAction(ContentView $view, OrderingMapper $orderingMapper)
    {
        $versionInfo = $view->getContent()->getVersionInfo();
        $contentInfo = $versionInfo->getContentInfo();

        $sectionService = $this->getRepository()->getSectionService();
        $section = $sectionService->loadSection($contentInfo->sectionId);

        $data = $orderingMapper->mapToForm($view->getLocation());

        $orderingForm = $this->createForm(
            Ordering::class,
            $data,
            [
                'current_sort_field' => $view->getLocation()->sortField,
            ]
        );

        $view->addParameters([
            'section' => $section,
            'contentInfo' => $contentInfo,
            'versionInfo' => $versionInfo,
            'creator' => $this->loadUser($contentInfo->ownerId),
            'lastContributor' => $this->loadUser($versionInfo->creatorId),
            'translations' => $this->getTranslations($versionInfo),
            'orderingForm' => $orderingForm->createView(),
        ]);

        return $view;
    }

    public function versionsTabAction(ContentView $view, VersionFilter $versionFilter)
    {
        $contentInfo = $view->getContent()->getVersionInfo()->getContentInfo();
        $contentService = $this->getRepository()->getContentService();
        $versions = $contentService->loadVersions($contentInfo);

        $authors = [];
        foreach ($versions as $version) {
            $authors[$version->id] = $this->loadUser($version->creatorId);
        }

        $translations = [];
        foreach ($versions as $version) {
            $translations[$version->id] = $this->getTranslations($version);
        }

        $view->addParameters([
            'draftVersions' => $versionFilter->filterDrafts($versions),
            'publishedVersions' => $versionFilter->filterPublished($versions),
            'archivedVersions' => $versionFilter->filterArchived($versions),
            'authors' => $authors,
            'translations' => $translations,
        ]);

        return $view;
    }

    public function locationsTabAction(ContentView $view, UiLocationService $uiLocationService)
    {
        $versionInfo = $view->getContent()->getVersionInfo();
        $contentInfo = $versionInfo->getContentInfo();

        if ($contentInfo->published) {
            $locations = $uiLocationService->loadLocations($contentInfo);

            $view->addParameters([
                'locations' => $locations,
            ]);
        }

        return $view;
    }

    public function translationsTabAction(ContentView $view)
    {
        $view->addParameters([
            'translations' => $this->getTranslations($view->getContent()->getVersionInfo()),
        ]);

        return $view;
    }

    protected function loadUser($userId)
    {
        $userService = $this->getRepository()->getUserService();

        return $this->getRepository()->sudo(function () use ($userId, $userService) {
            try {
                return $userService->loadUser($userId);
            } catch (NotFoundException $e) {
                return null;
            }
        });
    }

    protected function getTranslations(VersionInfo $versionInfo)
    {
        $languageRepository = $this->getRepository()->getContentLanguageService();

        $translations = [];
        foreach ($versionInfo->languageCodes as $languageCode) {
            $translations[] = $languageRepository->loadLanguage($languageCode);
        }

        return $translations;
    }
}
