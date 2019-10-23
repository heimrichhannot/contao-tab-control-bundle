<?php

/**
 * Content elements
 */

use HeimrichHannot\TabControlBundle\ContentElement\TabControlSeparatorElement;
use HeimrichHannot\TabControlBundle\ContentElement\TabControlStartElement;
use HeimrichHannot\TabControlBundle\ContentElement\TabControlStopElement;

$GLOBALS['TL_CTE']['tabs'][TabControlStartElement::TYPE]     = TabControlStartElement::class;
$GLOBALS['TL_CTE']['tabs'][TabControlSeparatorElement::TYPE] = TabControlSeparatorElement::class;
$GLOBALS['TL_CTE']['tabs'][TabControlStopElement::TYPE]      = TabControlStopElement::class;

$GLOBALS['TL_WRAPPERS']['start'][] = TabControlStartElement::TYPE;
$GLOBALS['TL_WRAPPERS']['separator'][] = TabControlSeparatorElement::TYPE;
$GLOBALS['TL_WRAPPERS']['stop'][] = TabControlStopElement::TYPE;