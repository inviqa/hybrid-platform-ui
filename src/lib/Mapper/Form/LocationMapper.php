<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\HybridPlatformUi\Mapper\Form;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;

/**
 * Maps location information to expected formats.
 */
class LocationMapper
{
    /**
     * Map locations and content to data required in form.
     *
     * @param eZ\Publish\API\Repository\Values\Content\Location[] $locations
     * @param eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo
     *
     * @return array
     */
    public function mapToForm(array $locations, ContentInfo $contentInfo)
    {
        $data = [
            'contentId' => $contentInfo->id,
            'locationIds' => [],
        ];

        foreach ($locations as $location) {
            $data['locationIds'][$location->id] = false;
        }

        return $data;
    }
}
