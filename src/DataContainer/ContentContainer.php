<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\TabControlBundle\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\ContentModel;
use Contao\DataContainer;
use HeimrichHannot\TabControlBundle\Controller\ContentElement\TabControlStartElementController;
use HeimrichHannot\TabControlBundle\Controller\ContentElement\TabControlStopElementController;
use HeimrichHannot\TabControlBundle\Helper\StructureTabHelper;

class ContentContainer
{
    public function __construct(protected StructureTabHelper $structureTabHelper)
    {
    }

    #[AsCallback(table: 'tl_content', target: 'config.onsubmit')]
    public function createTabControlElement(DataContainer $dc): void
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

    #[AsCallback(table: 'tl_content', target: 'config.ondelete')]
    public function deleteTabControlElement(DataContainer $dc): void
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
