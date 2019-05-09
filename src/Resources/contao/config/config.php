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
if (TL_MODE === 'FE')
{
    $GLOBALS['TL_JAVASCRIPT']['huh_contao-tab-control-bundle_bootstrap-tabs'] = 'bundles/contaotabcontrol/bootstrap-tabs.js';
    $GLOBALS['TL_JAVASCRIPT']['huh_contao-tab-control-bundle'] = 'bundles/contaotabcontrol/contao-tab-control-bundle.js';
}
else {
    $GLOBALS['TL_CSS']['huh_contao-tab-control-bundle_backend'] = 'bundles/contaotabcontrol/tabcontrol-backend.css';
}


$GLOBALS['TL_WRAPPERS']['start'][] = TabControlStartElement::TYPE;
$GLOBALS['TL_WRAPPERS']['separator'][] = TabControlSeperatorElement::TYPE;
$GLOBALS['TL_WRAPPERS']['stop'][] = TabControlStopElement::TYPE;