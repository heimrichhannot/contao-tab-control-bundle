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

class TabControlSeperatorElement extends ContentElement
{
    const TYPE = 'tabcontrolSeperator';

    protected $strTemplate = 'ce_tabcontrol_seperator_default';

    /**
     * Compile the content element
     */
    protected function compile()
    {
        if (TL_MODE == 'BE') {
            $this->Template           = new BackendTemplate('be_wildcard');
            $this->Template->wildcard = '### TABCONTROL: SEPERATOR ###<br>'.$this->tabControlHeadline;
            return;
        }

        $this->Template->tabControlHeadline = $this->tabControlHeadline;
        $this->Template->tabId = StringUtil::generateAlias($this->tabControlHeadline).'_'.$this->id;
        $this->Template->active = false;
    }
}