<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\TabControlBundle\Asset;


use HeimrichHannot\UtilsBundle\Container\ContainerUtil;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FrontendAssets
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var ContainerUtil
     */
    private $containerUtil;

    /**
     * FrontendAssets constructor.
     */
    public function __construct(ContainerInterface $container, ContainerUtil $containerUtil)
    {
        $this->container = $container;
        $this->containerUtil = $containerUtil;
    }

    /**
     * Setup the frontend assets needed for tab control bundle content elements
     */
    public function addFrontendAssets()
    {
        if ($this->containerUtil->isFrontend())
        {

            if ($this->container->has('huh.encore.asset.frontend'))
            {
                $this->container->get('huh.encore.asset.frontend')->addActiveEntrypoint('contao-tab-control-bundle');
            }
            $GLOBALS['TL_JAVASCRIPT']['huh_contao-tab-control-bundle_bootstrap-tabs'] = 'bundles/contaotabcontrol/bootstrap-tabs.js';
            $GLOBALS['TL_JAVASCRIPT']['huh_contao-tab-control-bundle']                = 'bundles/contaotabcontrol/contao-tab-control-bundle.js';
        } else {
            $GLOBALS['TL_CSS']['huh_contao-tab-control-bundle_backend'] = 'bundles/contaotabcontrol/tabcontrol-backend.css';
        }
    }
}