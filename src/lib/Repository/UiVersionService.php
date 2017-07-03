<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\HybridPlatformUi\Repository;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use EzSystems\HybridPlatformUi\Repository\Values\Content\UiVersionInfo;

/**
 * Service for allowing deletion of versions.
 */
class UiVersionService
{
    /**
     * @var ContentService
     */
    private $contentService;

    /**
     * @var UiSudoService
     */
    private $uiSudoService;

    /**
     * @var UiTranslationService
     */
    private $uiTranslationService;

    public function __construct(
        ContentService $contentService,
        UiSudoService $uiSudoService,
        UiTranslationService $uiTranslationService
    ) {
        $this->contentService = $contentService;
        $this->uiSudoService = $uiSudoService;
        $this->uiTranslationService = $uiTranslationService;
    }

    /**
     * Load versions and adds the author and translations.
     *
     * @param ContentInfo $contentInfo
     *
     * @return UiVersionInfo[]
     */
    public function loadVersions(ContentInfo $contentInfo)
    {
        $versions = $this->contentService->loadVersions($contentInfo);

        return $this->buildUiVersions($versions);
    }

    /**
     * Deletes a version based on the contentId and versionNo.
     *
     * @param int $contentId
     * @param int $versionNo
     */
    public function deleteVersion(int $contentId, int $versionNo)
    {
        $versionInfo = $this->contentService->loadVersionInfo(
            $this->contentService->loadContentInfo($contentId),
            $versionNo
        );

        $this->contentService->deleteVersion($versionInfo);
    }

    private function buildUiVersions(array $versions)
    {
        return array_map(
            function (VersionInfo $versionInfo) {
                $properties = [
                    'author' => $this->uiSudoService->loadUser($versionInfo->creatorId),
                    'translations' => $this->uiTranslationService->loadTranslations($versionInfo),
                ];

                return new UiVersionInfo($versionInfo, $properties);
            },
            $versions
        );
    }
}