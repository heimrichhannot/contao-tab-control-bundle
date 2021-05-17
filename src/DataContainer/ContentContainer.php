<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\TabControlBundle\DataContainer;

use Contao\ContentModel;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use HeimrichHannot\TabControlBundle\Controller\ContentElement\TabControlStartElementController;
use HeimrichHannot\TabControlBundle\Controller\ContentElement\TabControlStopElementController;
use HeimrichHannot\TabControlBundle\Helper\StructureTabHelper;

class ContentContainer
{
    protected StructureTabHelper $structureTabHelper;

    public function __construct(StructureTabHelper $structureTabHelper)
    {
        $this->structureTabHelper = $structureTabHelper;
    }

    /**
     * @Callback(table="tl_content", target="config.onsubmit")
     */
    public function createTabControlElement(DataContainer $dc)
    {
        if (TabControlStartElementController::TYPE !== $dc->activeRecord->type) {
            return;
        }

        $tabData = [
            'id' => $dc->activeRecord->id,
            'pid' => $dc->activeRecord->pid,
            'ptable' => $dc->activeRecord->ptable,
        ];

        $this->structureTabHelper->structureTabs($tabData);

        if (!isset($tabData['elements']) || \count($tabData['elements']) > 1) {
            return;
        }

        $endElement = new ContentModel();
        $endElement->pid = $dc->activeRecord->pid;
        $endElement->ptable = $dc->activeRecord->ptable;
        $endElement->tstamp = $dc->activeRecord->tstamp;
        $endElement->sorting = $dc->activeRecord->sorting * 2;
        $endElement->type = TabControlStopElementController::TYPE;
        $endElement->save();
    }

    /**
     * @Callback(table="tl_content", target="config.ondelete")
     */
    public function deleteTabControlElement(DataContainer $dc)
    {
        if (TabControlStartElementController::TYPE !== $dc->activeRecord->type && TabControlStopElementController::TYPE !== $dc->activeRecord->type) {
            return;
        }

        $tabData = [
            'id' => $dc->activeRecord->id,
            'pid' => $dc->activeRecord->pid,
            'ptable' => $dc->activeRecord->ptable,
        ];

        $this->structureTabHelper->structureTabs($tabData);

        if (!isset($tabData['elements']) || \count($tabData['elements']) < 2) {
            return;
        }

        foreach ($tabData['elements'] as $element) {
            if ($element['id'] === $dc->activeRecord->id) {
                continue;
            }
            $contentElement = ContentModel::findByPk($element['id']);

            if ($contentElement) {
                $contentElement->delete();
            }
        }
    }
}
