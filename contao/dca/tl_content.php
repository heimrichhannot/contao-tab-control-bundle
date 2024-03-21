<?php

use HeimrichHannot\TabControlBundle\Controller\ContentElement\TabControlSeparatorElementController;
use HeimrichHannot\TabControlBundle\Controller\ContentElement\TabControlStartElementController;
use HeimrichHannot\TabControlBundle\Controller\ContentElement\TabControlStopElementController;

$dca = &$GLOBALS['TL_DCA']['tl_content'];

/**
 * Palettes
 */
$dca['palettes']['__selector__'][] = 'tabControlAddLink';

$dca['palettes'][TabControlStartElementController::TYPE]     =
    '{type_legend},type,headline;{tab_legend},tabControlHeadline;{tab_section_legend},tabControlRememberLastTab,tabControlAddLink;';
$dca['palettes'][TabControlSeparatorElementController::TYPE] =
    '{type_legend},type;{tab_legend},tabControlHeadline;{tab_section_legend},tabControlAddLink;';
$dca['palettes'][TabControlStopElementController::TYPE]      =
    '{type_legend},type;';

/**
 * Subpalettes
 */
$dca['subpalettes']['tabControlAddLink'] = 'tabControlLink,tabControlTarget';

/**
 * Fields
 */
$fields = [
    'tabControlHeadline'        => [
        'exclude'   => true,
        'inputType' => 'text',
        'eval'      => ['maxlength' => 255, 'allowHtml' => true, 'tl_class' => 'long', "mandatory" => true],
        'sql'       => "varchar(255) NOT NULL default ''"
    ],
    'tabControlRememberLastTab' => [
        'inputType' => 'checkbox',
        'exclude'   => true,
        'eval'      => [
            'tl_class' => 'w50 clr',
        ],
        'sql'       => "char(1) NOT NULL default ''",
    ],
    'tabControlAddLink'         => [
        'inputType' => 'checkbox',
        'exclude'   => true,
        'eval'      => [
            'tl_class'       => 'w50 clr',
            'submitOnChange' => true
        ],
        'sql'       => "char(1) NOT NULL default ''",
    ],
    'tabControlLink'            => [
        'label'     => &$GLOBALS['TL_LANG']['MSC']['url'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'text',
        'eval'      => ['mandatory' => true, 'rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 255, 'dcaPicker' => true, 'addWizardClass' => false, 'tl_class' => 'w50'],
        'sql'       => "varchar(255) NOT NULL default ''"
    ],
    'tabControlTarget'          => [
        'inputType' => 'checkbox',
        'exclude'   => true,
        'eval'      => [
            'tl_class' => 'w50 clr',
        ],
        'sql'       => "char(1) NOT NULL default ''",
    ]
];

$dca['fields'] = array_merge(is_array($dca['fields']) ? $dca['fields'] : [], $fields);
