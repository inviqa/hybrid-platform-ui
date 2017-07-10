<?php
/**
 * File containing the VersionController class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\HybridPlatformUiBundle\Controller;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Content;
use EzSystems\HybridPlatformUi\Form\UiFormFactory;
use EzSystems\HybridPlatformUi\Repository\UiVersionService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class VersionController extends TabController
{
    /**
     * @var UiVersionService
     */
    private $uiVersionService;

    public function __construct(
        UiVersionService $uiVersionService,
        ContentService $contentService,
        RouterInterface $router
    ) {
        $this->uiVersionService = $uiVersionService;
        parent::__construct($router, $contentService);
    }

    public function draftActionsAction(
        Content $content,
        Request $request,
        UiFormFactory $formFactory
    ) {
        $draftActionsForm = $formFactory->createVersionsDraftActionForm();
        $draftActionsForm->handleRequest($request);

        if ($draftActionsForm->isValid()) {
            $this->deleteVersionsBasedOnFormSubmit($draftActionsForm, $content->id);
        }

        $redirectLocationId = $request->query->get('redirectLocationId', $content->contentInfo->mainLocationId);

        return $this->reloadTab('versions', $content->id, $redirectLocationId);
    }

    public function archiveActionsAction(
        Content $content,
        Request $request,
        UiFormFactory $formFactory
    ) {
        $archiveActionsForm = $formFactory->createVersionsArchivedActionForm();
        $archiveActionsForm->handleRequest($request);

        if ($archiveActionsForm->isValid()) {
            $this->deleteVersionsBasedOnFormSubmit($archiveActionsForm, $content->id);
            $this->createDraftVersionBasedOnFormSubmit($archiveActionsForm, $content->id);
        }

        $redirectLocationId = $request->query->get('redirectLocationId', $content->contentInfo->mainLocationId);

        return $this->reloadTab('versions', $content->id, $redirectLocationId);
    }

    private function deleteVersionsBasedOnFormSubmit(FormInterface $form, $contentId)
    {
        $selectedIds = array_keys($form->get('versionIds')->getData());

        if ($form->get('delete')->isClicked()) {
            foreach ($selectedIds as $versionId) {
                $this->uiVersionService->deleteVersion((int) $contentId, $versionId);
            }
        }
    }

    private function createDraftVersionBasedOnFormSubmit(FormInterface $form, $contentId)
    {
        if ($form->get('new_draft')->isClicked()) {
            $versionId = key($form->get('versionIds')->getData());
            $this->uiVersionService->createDraft($contentId, $versionId);
        }
    }
}
