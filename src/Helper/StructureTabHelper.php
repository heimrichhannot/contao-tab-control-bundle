<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\TabControlBundle\Helper;

use Contao\ContentModel;
use Contao\Controller;
use Contao\StringUtil;
use HeimrichHannot\TabControlBundle\Controller\ContentElement\TabControlSeparatorElementController;
use HeimrichHannot\TabControlBundle\Controller\ContentElement\TabControlStartElementController;
use HeimrichHannot\TabControlBundle\Controller\ContentElement\TabControlStopElementController;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StructureTabHelper
{
    private array $tabsStartStopCache = [];
    private Utils $utils;

    /**
     * StructureTabHelper constructor.
     */
    public function __construct(Utils $utils)
    {
        $this->utils = $utils;
    }

    public function getTabDataForContentElement(int $id, int $pid, string $ptable)
    {
        $tabData = ['id' => $id, 'pid' => $pid, 'ptable' => $ptable];
        $this->structureTabs($tabData, '', ['ptable' => $ptable]);

        $tabs = [];

        if (isset($tabData['elements'])) {
            foreach ($tabData['elements'] as $element) {
                if (\in_array($element['type'], [TabControlStartElementController::TYPE, TabControlSeparatorElementController::TYPE])) {
                    $tab = [];
                    $tab['headline'] = $element['tabControlHeadline'];
                    $tab['tabId'] = StringUtil::generateAlias($element['tabControlHeadline']).'_'.$element['id'];
                    $tab['active'] = (int) $element['id'] === $id;
                    $tab['id'] = $element['id'];
                    $tab['addTabLink'] = $element['tabControlAddLink'];
                    $tab['tabLink'] = ((false === strpos($element['tabControlLink'], 'http')) ? '/' : '').Controller::replaceInsertTags($element['tabControlLink']);
                    $tab['openLinkInNewTab'] = $element['tabControlTarget'];

                    $tabs[] = $tab;
                }
            }
        }

        return $tabs;
    }

    /**
     * @return array
     */
    public function structureTabsByContentElement(ContentModel $element, string $prefix = '', array $config = [])
    {
        $data['id'] = $element->id;
        $data['pid'] = $element->pid;
        $data['ptable'] = $element->ptable;
        $this->structureTabs($data, $prefix, $config);

        return $data;
    }

    /**
     * @param array  $data   data describing the tab
     * @param string $prefix The prefix for the flags
     * @param array  $config Options: startElement, separatorElement, stopElement
     */
    public function structureTabs(array &$data, string $prefix = '', array $config = [])
    {
        if (!isset($data['id']) || !isset($data['pid'])) {
            return;
        }

        if (!isset($data['ptable'])) {
            $data['ptable'] = 'tl_article';
        }

        $startElement = TabControlStartElementController::TYPE;

        if (isset($config['startElement'])) {
            $startElement = $config['startElement'];
        }
        $separatorElement = TabControlSeparatorElementController::TYPE;

        if (isset($config['separatorElement'])) {
            $separatorElement = $config['separatorElement'];
        }
        $stopElement = TabControlStopElementController::TYPE;

        if (isset($config['stopElement'])) {
            $stopElement = $config['stopElement'];
        }

        $cacheKey = $data['ptable'].'_'.$data['pid'];

        if (!isset($this->tabsStartStopCache[$cacheKey])) {
            if (null !== ($elements = $this->utils->model()->findModelInstancesBy(
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

                $closed = true;

                $this->buildCache($elements, $cacheKey, $startElement, $separatorElement, $stopElement);

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

                        if ($i === \count($elementGroup) - 1) {
                            $data[$prefix.'last'] = true;
                        }

                        $data[$prefix.'parentId'] = $elementGroup[0]['id'];
                        $data['elements'] = $elementGroup;
                        $data['current'] = $element;

                        break 2;
                    }
                }
            }
        }
    }

    /**
     * @param $elements
     * @param string $cacheKey
     * @param string $startElement
     * @param string $separatorElement
     * @param string $stopElement
     * @param array $processedElements
     * @return array
     */
    private function buildCache(&$elements, string $cacheKey, string $startElement, string $separatorElement, string $stopElement, array $processedElements = []): array
    {
        $closed = true;
        $iteration = \count($this->tabsStartStopCache[$cacheKey]);

        foreach ($elements as $i => $element) {
            if (\in_array($element->id, $processedElements)) {
                continue;
            }

            if ($startElement === $element->type) {
                if (\count($this->tabsStartStopCache[$cacheKey]) < 1) {
                    $this->tabsStartStopCache[$cacheKey][] = [];
                }

                if (!$closed) {
                    $this->tabsStartStopCache[$cacheKey][] = [];
                    $processedElements = $this->buildCache($elements, $cacheKey, $startElement, $separatorElement, $stopElement, $processedElements);
                } else {
                    $this->tabsStartStopCache[$cacheKey][$iteration][] = $element->row();
                    $closed = false;
                }
            } elseif ($separatorElement === $element->type) {
                $this->tabsStartStopCache[$cacheKey][$iteration][] = $element->row();
            } elseif ($stopElement === $element->type) {
                $this->tabsStartStopCache[$cacheKey][$iteration][] = $element->row();
                $this->tabsStartStopCache[$cacheKey][] = [];
                ++$iteration;
                $closed = true;

                if ($iteration > 0) {
                    $processedElements[] = $element->id;

                    return $processedElements;
                }
            }
            $processedElements[] = $element->id;
        }

        return $processedElements;
    }
}
