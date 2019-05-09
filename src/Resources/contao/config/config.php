<?php

/**
 * Content elements
 */

use HeimrichHannot\TabControlBundle\ContentElement\TabControlSeperatorElement;
use HeimrichHannot\TabControlBundle\ContentElement\TabControlStartElement;
use HeimrichHannot\TabControlBundle\ContentElement\TabControlStopElement;

$GLOBALS['TL_CTE']['tabs'][TabControlStartElement::TYPE]     = TabControlStartElement::class;
$GLOBALS['TL_CTE']['tabs'][TabControlSeperatorElement::TYPE] = TabControlSeperatorElement::class;
$GLOBALS['TL_CTE']['tabs'][TabControlStopElement::TYPE]      = TabControlStopElement::class;

/**
 * Assets
 */
$GLOBALS['TL_JAVASCRIPT']['huh_contao-tab-control-bundle_bootstrap-tabs'] = 'bundles/contaotabcontrol/js/bootstrap-tabs.js';
$GLOBALS['TL_JAVASCRIPT']['huh_contao-tab-control-bundle'] = 'bundles/contaotabcontrol/js/contao-tab-control-bundle.js';

$GLOBALS['TL_WRAPPERS']['start'][] = TabControlStartElement::TYPE;
$GLOBALS['TL_WRAPPERS']['separator'][] = TabControlSeperatorElement::TYPE;
$GLOBALS['TL_WRAPPERS']['stop'][] = TabControlStopElement::TYPE;