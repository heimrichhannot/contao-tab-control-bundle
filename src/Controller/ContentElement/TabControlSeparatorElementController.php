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
use Contao\StringUtil;
use Contao\Template;
use HeimrichHannot\TabControlBundle\Helper\StructureTabHelper;
use HeimrichHannot\UtilsBundle\Container\ContainerUtil;
use HeimrichHannot\UtilsBundle\Model\ModelUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ContentElement(TabControlSeparatorElementController::TYPE,category="tabs",template="ce_tabcontrol_separator")
 */
class TabControlSeparatorElementController extends AbstractContentElementController
{
    const TYPE = 'tabcontrolSeparator';

    /**
     * @var ModelUtil
     */
    protected ContainerUtil      $containerUtil;
    protected StructureTabHelper $structureTabHelper;

    public function __construct(ContainerUtil $containerUtil, StructureTabHelper $structureTabHelper)
    {
        $this->containerUtil      = $containerUtil;
        $this->structureTabHelper = $structureTabHelper;
    }

    protected function getResponse(Template $template, ContentModel $element, Request $request): ?Response
    {
        if ($this->containerUtil->isBackend()) {
            $template = new BackendTemplate('be_tabs_control');
        }

        $tabs = $this->structureTabHelper->getTabDataForContentElement($element->id, $element->pid, $element->ptable);

        $template->id                 = $element->id;
        $template->tabs               = $tabs;
        $template->tabControlHeadline = $element->tabControlHeadline;
        $template->tabId              = StringUtil::generateAlias($element->tabControlHeadline) . '_' . $element->id;
        $template->active             = false;

        return $template->getResponse();
    }
}
