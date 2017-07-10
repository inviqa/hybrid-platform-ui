<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\HybridPlatformUi\Repository;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;

/**
 * Service for allowing deletion of versions.
 */
class UiVersionService
{
    /**
     * @var ContentService
     */
    private $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * Deletes a version based on the contentId and versionNo.
     *
     * @param int $contentId
     * @param int $versionNo
     */
    public function deleteVersion(int $contentId, int $versionNo)
    {
        $versionInfo = $this->loadVersionInfo(
            $this->loadContentInfo($contentId),
            $versionNo
        );

        $this->contentService->deleteVersion($versionInfo);
    }

    /**
     * Creates a new draft based on the content and verisionNo.
     *
     * @param $contentId
     * @param $versionNo
     */
    public function createDraft($contentId, $versionNo)
    {
        $contentInfo = $this->loadContentInfo($contentId);
        $versionInfo = $this->loadVersionInfo(
            $this->loadContentInfo($contentId),
            $versionNo
        );

        $this->contentService->createContentDraft($contentInfo, $versionInfo);
    }

    private function loadContentInfo($contentId)
    {
        return $this->contentService->loadContentInfo($contentId);
    }

    private function loadVersionInfo(ContentInfo $contentInfo, $versionNo)
    {
        return $this->contentService->loadVersionInfo($contentInfo, $versionNo);
    }
}
