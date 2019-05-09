<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\TabControlBundle\ContentElement;


use Contao\BackendTemplate;
use Contao\ContentElement;
use Contao\StringUtil;
use Contao\System;

class TabControlStartElement extends ContentElement
{
    const TYPE = 'tabcontrolStart';

    protected $strTemplate = 'ce_tabcontrol_start_default';

    /**
     * Compile the content element
     */
    protected function compile()
    {
        $container = System::getContainer();

        $this->Template->headline = $this->headline;
        if (TL_MODE == 'BE') {
            $this->Template           = new BackendTemplate('be_tabs_control');
//            $this->Template->wildcard = '### TABCONTROL: START ###<br>'.$this->tabControlHeadline;
        }

        $tabs = System::getContainer()->get('huh.tab_control.helper.structure_tabs')->getTabDataForContentElement($this->id, $this->pid, $this->ptable);

        $this->Template->id = $this->id;
        $this->Template->tabs = $tabs;
        $this->Template->tabControlHeadline = $this->tabControlHeadline;
        $this->Template->tabId = StringUtil::generateAlias($this->tabControlHeadline).'_'.$this->id;
        $this->Template->active = true;
        $this->Template->rememberLastTab = $this->tabControlRememberLastTab;
    }
}