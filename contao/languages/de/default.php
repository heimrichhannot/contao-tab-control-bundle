<?php

use HeimrichHannot\TabControlBundle\Controller\ContentElement\TabControlSeparatorElementController;
use HeimrichHannot\TabControlBundle\Controller\ContentElement\TabControlStartElementController;
use HeimrichHannot\TabControlBundle\Controller\ContentElement\TabControlStopElementController;

$GLOBALS['TL_LANG']['CTE']['tabs'] = 'Tabs';

$GLOBALS['TL_LANG']['CTE'][TabControlStartElementController::TYPE]     = ['Tabs Start', ''];
$GLOBALS['TL_LANG']['CTE'][TabControlSeparatorElementController::TYPE] = ['Tabs Trenner', ''];
$GLOBALS['TL_LANG']['CTE'][TabControlStopElementController::TYPE]      = ['Tabs Endelement', ''];
