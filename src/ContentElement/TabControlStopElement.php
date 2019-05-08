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

class TabControlStopElement extends ContentElement
{
    const TYPE = 'tabcontrolStop';

    protected $strTemplate = 'ce_tabcontrol_stop_default';

    /**
     * Compile the content element
     */
    protected function compile()
    {
        if (TL_MODE == 'BE') {
            $this->Template           = new BackendTemplate('be_wildcard');
            $this->Template->wildcard = '### TABCONTROL: STOP ###';
            return;
        }
    }
}