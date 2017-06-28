<?php

namespace spec\EzSystems\HybridPlatformUi\Mapper\Form;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\Location;
use PhpSpec\ObjectBehavior;

class LocationMapperSpec extends ObjectBehavior
{
    function it_should_map_locations_to_form()
    {
        $contentId = 1;
        $contentInfo = new ContentInfo(['id' => $contentId]);

        $firstLocationId = 1;
        $secondLocationId = 2;

        $locations = [
            new Location(['id' => $firstLocationId]),
            new Location(['id' => $secondLocationId]),
        ];

        $expectedData = [
            'contentId' => $contentId,
            'locationIds' => [
                $firstLocationId => false,
                $secondLocationId => false,
            ],
        ];

        $this->mapToForm($locations, $contentInfo)->shouldBeLike($expectedData);
    }
}
