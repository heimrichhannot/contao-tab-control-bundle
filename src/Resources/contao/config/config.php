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