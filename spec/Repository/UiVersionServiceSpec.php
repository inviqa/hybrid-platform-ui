<?php

namespace spec\EzSystems\HybridPlatformUi\Repository;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\Core\Repository\Values\User\User;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use EzSystems\HybridPlatformUi\Repository\UiSudoService;
use EzSystems\HybridPlatformUi\Repository\UiTranslationService;
use EzSystems\HybridPlatformUi\Repository\Values\Content\UiVersionInfo;
use PhpSpec\ObjectBehavior;

class UiVersionServiceSpec extends ObjectBehavior
{
    function let(ContentService $contentService, UiSudoService $sudoService, UiTranslationService $translationService)
    {
        $this->beConstructedWith($contentService, $sudoService, $translationService);
    }

    function it_should_delete_versions(
        ContentService $contentService,
        ContentInfo $contentInfo,
        VersionInfo $versionInfo
    ) {
        $contentId = '1';
        $versionNumber = 10;

        $contentService->loadContentInfo($contentId)->willReturn($contentInfo)->shouldBeCalled();
        $contentService->loadVersionInfo($contentInfo, $versionNumber)->willReturn($versionInfo)->shouldBeCalled();
        $contentService->deleteVersion($versionInfo)->shouldBeCalled();

        $this->deleteVersion($contentId, $versionNumber);
    }

    function it_loads_ui_versions_with_translation_and_author(
        ContentService $contentService,
        UiSudoService $sudoService,
        UiTranslationService $translationService
    ) {
        $creatorId = 1;
        $contentInfo = new ContentInfo();
        $versionInfo = new VersionInfo(['creatorId' => $creatorId]);
        $user = new User();
        $language = new Language();

        $contentService->loadVersions($contentInfo)->willReturn([$versionInfo]);
        $sudoService->loadUser($creatorId)->willReturn($user);
        $translationService->loadTranslations($versionInfo)->willReturn([$language]);

        $uiVersion = new UiVersionInfo($versionInfo, ['author' => $user, 'translations' => [$language]]);

        $this->loadVersions($contentInfo)->shouldBeLike([$uiVersion]);
    }
}