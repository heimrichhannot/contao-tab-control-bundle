<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\TabControlBundle\Controller\ContentElement;

use Contao\BackendTemplate;
use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\Template;
use HeimrichHannot\TabControlBundle\Asset\FrontendAsset;
use HeimrichHannot\TabControlBundle\Helper\StructureTabHelper;
use HeimrichHannot\UtilsBundle\Container\ContainerUtil;
use HeimrichHannot\UtilsBundle\Model\ModelUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ContentElement(TabControlStopElementController::TYPE,category="tabs",template="ce_tabcontrol_stop")
 */
class TabControlStopElementController extends AbstractContentElementController
{
    const TYPE = 'tabcontrolStop';

    /**
     * @var ModelUtil
     */
    protected ContainerUtil      $containerUtil;
    protected StructureTabHelper $structureTabHelper;
    protected FrontendAsset      $frontendAsset;

    public function __construct(ContainerUtil $containerUtil, StructureTabHelper $structureTabHelper, FrontendAsset $frontendAsset)
    {
        $this->containerUtil = $containerUtil;
        $this->structureTabHelper = $structureTabHelper;
        $this->frontendAsset = $frontendAsset;
    }

    protected function getResponse(Template $template, ContentModel $element, Request $request): ?Response
    {
        if ($this->containerUtil->isBackend()) {
            $template = new BackendTemplate('be_tabs_control');
        }

        $tabs = $this->structureTabHelper->getTabDataForContentElement($element->id, $element->pid, $element->ptable);

        $template->id = $element->id;
        $template->tabs = $tabs;
        $template->isStopElement = true;

        return $template->getResponse();
    }
}
