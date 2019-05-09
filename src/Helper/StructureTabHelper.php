<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\TabControlBundle\Helper;


use Contao\StringUtil;
use function count;
use HeimrichHannot\TabControlBundle\ContentElement\TabControlSeperatorElement;
use HeimrichHannot\TabControlBundle\ContentElement\TabControlStartElement;
use HeimrichHannot\TabControlBundle\ContentElement\TabControlStopElement;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StructureTabHelper
{
    /**
     * @var ContainerInterface
     */
    private $container;
    private $tabsStartStopCache = [];

    /**
     * StructureTabHelper constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getTabDataForContentElement(int $id, int $pid, string $ptable)
    {
        $tabData = ['id' => $id, 'pid' => $pid, 'ptable' => $ptable];
        $this->structureTabs($tabData, '', ['ptable' => $ptable]);

        $tabs = [];
        if (isset($tabData['elements']))
        {
            foreach ($tabData['elements'] as $element)
            {
                if (in_array($element['type'], [TabControlStartElement::TYPE, TabControlSeperatorElement::TYPE])) {
                    $tab = [];
                    $tab['headline'] = $element['tabControlHeadline'];
                    $tab['tabId'] = StringUtil::generateAlias($element['tabControlHeadline']).'_'.$element['id'];
                    $tab['active'] = $element['id'] === $id;
                    $tab['id'] = $element['id'];
                    $tabs[] = $tab;
                }
            }
        }
        return $tabs;
    }


    /**
     * @param array $data Data describing the tab.
     * @param string $prefix The prefix for the flags
     * @param array $config Options: ptable
     */
    public function structureTabs(array &$data, string $prefix = '', array $config = [])
    {
        if (!isset($data['id']) || !isset($data['pid'])) {
            return;
        }

        if (!isset($data['ptable']))
        {
            $data['ptable'] = 'tl_article';
        }

        $cacheKey = $data['ptable'].'_'.$data['pid'];

        if (!isset($this->tabsStartStopCache[$cacheKey])) {
            if (null !== ($elements = $this->container->get('huh.utils.model')->findModelInstancesBy(
                    'tl_content',
                    [
                        'tl_content.ptable=?',
                        'tl_content.pid=?',
                        'tl_content.invisible!=1',
                    ],
                    [
                        $data['ptable'],
                        $data['pid'],
                    ],
                    [
                        'order' => 'sorting ASC',
                    ]
                ))) {
                $this->tabsStartStopCache[$cacheKey] = [];

                foreach ($elements as $i => $element) {
                    if (TabControlStartElement::TYPE === $element->type)
                    {
                        if (count($this->tabsStartStopCache[$cacheKey]) < 1) {
                            $this->tabsStartStopCache[$cacheKey][] = [];
                        }
                        $this->tabsStartStopCache[$cacheKey][count($this->tabsStartStopCache[$cacheKey]) - 1][] = $element->row();
                    }
                    elseif (TabControlSeperatorElement::TYPE === $element->type) {
                        $this->tabsStartStopCache[$cacheKey][count($this->tabsStartStopCache[$cacheKey]) - 1][] = $element->row();
                    }
                    elseif (TabControlStopElement::TYPE === $element->type)
                    {
                        $this->tabsStartStopCache[$cacheKey][count($this->tabsStartStopCache[$cacheKey]) - 1][] = $element->row();
                        $this->tabsStartStopCache[$cacheKey][] = [];
                    }
                }

                // remove trailing empty arrays
                $cleaned = [];

                foreach ($this->tabsStartStopCache[$cacheKey] as $elementGroup) {
                    if (!empty($elementGroup)) {
                        $cleaned[] = $elementGroup;
                    }
                }

                $this->tabsStartStopCache[$cacheKey] = $cleaned;
            }
        }

        if (isset($this->tabsStartStopCache[$cacheKey]) && \is_array($this->tabsStartStopCache[$cacheKey])) {
            foreach ($this->tabsStartStopCache[$cacheKey] as $elementGroup) {
                foreach ($elementGroup as $i => $element) {
                    if ($data['id'] == $element['id']) {
                        if (0 === $i) {
                            $data[$prefix.'first'] = true;
                        }

                        if ($i === count($elementGroup) - 1) {
                            $data[$prefix.'last'] = true;
                        }

                        $data[$prefix.'parentId'] = $elementGroup[0]['id'];
                        $data['elements'] = $elementGroup;

                        break 2;
                    }
                }
            }
        }
    }
}