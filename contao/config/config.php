<?php

use HeimrichHannot\TabControlBundle\Controller\ContentElement\TabControlStartElementController;
use HeimrichHannot\TabControlBundle\Controller\ContentElement\TabControlSeparatorElementController;
use HeimrichHannot\TabControlBundle\Controller\ContentElement\TabControlStopElementController;

/**
 * Content elements
 */
$GLOBALS['TL_WRAPPERS']['start'][]     = TabControlStartElementController::TYPE;
$GLOBALS['TL_WRAPPERS']['separator'][] = TabControlSeparatorElementController::TYPE;
$GLOBALS['TL_WRAPPERS']['stop'][]      = TabControlStopElementController::TYPE;
