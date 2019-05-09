<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\TabControlBundle\DataContainer;


use Contao\ContentModel;
use Contao\DataContainer;
use HeimrichHannot\TabControlBundle\ContentElement\TabControlStartElement;
use HeimrichHannot\TabControlBundle\ContentElement\TabControlStopElement;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContentContainer
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function createTabControlElement(DataContainer $dc)
    {
        if ($dc->activeRecord->type !== TabControlStartElement::TYPE)
        {
            return;
        }

        $tabData = [
            'id' => $dc->activeRecord->id,
            'pid' => $dc->activeRecord->pid,
            'ptable' => $dc->activeRecord->ptable
        ];
        $this->container->get('huh.tab_control.helper.structure_tabs')->structureTabs($tabData);

        if (!isset($tabData['elements']) || count($tabData['elements']) > 1)
        {
            return;
        }

        $endElement = new ContentModel();
        $endElement->pid = $dc->activeRecord->pid;
        $endElement->ptable = $dc->activeRecord->ptable;
        $endElement->tstamp = $dc->activeRecord->tstamp;
        $endElement->sorting = $dc->activeRecord->sorting * 2;
        $endElement->type = TabControlStopElement::TYPE;
        $endElement->save();
    }

    public function deleteTabControlElement(DataContainer $dc)
    {
        if ($dc->activeRecord->type !== TabControlStartElement::TYPE && $dc->activeRecord->type !== TabControlStopElement::TYPE)
        {
            return;
        }

        $tabData = [
            'id' => $dc->activeRecord->id,
            'pid' => $dc->activeRecord->pid,
            'ptable' => $dc->activeRecord->ptable
        ];

        $this->container->get('huh.tab_control.helper.structure_tabs')->structureTabs($tabData);

        if (!isset($tabData['elements']) || count($tabData['elements']) < 2)
        {
            return;
        }

        foreach ($tabData['elements'] as $element)
        {
            if ($element['id'] === $dc->activeRecord->id) {
                continue;
            }
            $contentElement = ContentModel::findByPk($element['id']);
            if ($contentElement)
            {
                $contentElement->delete();
            }
        }
    }


}