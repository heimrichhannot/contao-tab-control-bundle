<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\TabControlBundle\Controller\ContentElement;

use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\BackendTemplate;
use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\StringUtil;
use Contao\Template;
use HeimrichHannot\TabControlBundle\Asset\FrontendAsset;
use HeimrichHannot\TabControlBundle\Helper\StructureTabHelper;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(TabControlStartElementController::TYPE, category: 'tabs', template: 'ce_tabcontrol_start')]
class TabControlStartElementController extends AbstractContentElementController
{
    public const TYPE = 'tabcontrolStart';
    private readonly Utils $utils;

    public function __construct(protected StructureTabHelper $structureTabHelper, protected FrontendAsset $frontendAsset, Utils $utils)
    {
        $this->utils = $utils;
    }

    protected function getResponse(Template $template, ContentModel $element, Request $request): Response
    {
        if ($this->utils->container()->isBackend()) {
            $template = new BackendTemplate('be_tabs_control');
        }

        $this->frontendAsset->addFrontendAsset();

        $tabs = $this->structureTabHelper->getTabDataForContentElement($element->id, $element->pid, $element->ptable);

        $template->id = $element->id;
        $template->tabs = $tabs;
        $template->tabControlHeadline = $element->tabControlHeadline;
        $template->tabId = StringUtil::generateAlias($element->tabControlHeadline).'_'.$element->id;
        $template->active = true;
        $template->rememberLastTab = $element->tabControlRememberLastTab;

        return $template->getResponse();
    }
}
