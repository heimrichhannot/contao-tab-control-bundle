<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$dca = &$GLOBALS['TL_DCA']['tl_content'];

$GLOBALS['TL_DCA']['tl_content']['config']['onsubmit_callback'][] = ['huh.tab_control.data_container.content','createTabControlElement'];
$GLOBALS['TL_DCA']['tl_content']['config']['ondelete_callback'][] = ['huh.tab_control.data_container.content','deleteTabControlElement'];

$dca['palettes'][\HeimrichHannot\TabControlBundle\ContentElement\TabControlStartElement::TYPE] = '{type_legend},type,headline;{tab_legend},tabControlHeadline;{tab_section_legend},tabControlRememberLastTab;';
$dca['palettes'][\HeimrichHannot\TabControlBundle\ContentElement\TabControlSeperatorElement::TYPE] = '{type_legend},type;{tab_legend},tabControlHeadline;';
$dca['palettes'][\HeimrichHannot\TabControlBundle\ContentElement\TabControlStopElement::TYPE] = '{type_legend},type;';

$dca['fields']['tabControlHeadline'] = [
    'label'                   => &$GLOBALS['TL_LANG']['tl_content']['tabControlHeadline'],
    'exclude'                 => true,
    'inputType'               => 'text',
    'eval'                    => ['maxlength' =>255, 'allowHtml' =>true, 'tl_class' =>'long', "mandatory" => true],
    'sql'                     => "varchar(255) NOT NULL default ''"
];

$dca['fields']['tabControlRememberLastTab'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_content']['tabControlRememberLastTab'],
    'inputType' => 'checkbox',
    'exclude'   => true,
    'eval'      => [
        'tl_class' => 'w50 clr',
    ],
    'sql'       => "char(1) NOT NULL default ''",
];