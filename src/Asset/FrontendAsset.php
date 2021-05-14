<?php

namespace HeimrichHannot\TabControlBundle\Asset;

use HeimrichHannot\UtilsBundle\Container\ContainerUtil;

class FrontendAsset
{
    /**
     * @var \HeimrichHannot\EncoreBundle\Asset\FrontendAsset
     */
    protected $encoreFrontendAsset;
    /**
     * @var ContainerUtil
     */
    private $containerUtil;

    /**
     * FrontendAsset constructor.
     */
    public function __construct(ContainerUtil $containerUtil)
    {
        $this->containerUtil = $containerUtil;
    }

    public function setEncoreFrontendAsset(\HeimrichHannot\EncoreBundle\Asset\FrontendAsset $encoreFrontendAsset): void
    {
        $this->encoreFrontendAsset = $encoreFrontendAsset;
    }

    public function addFrontendAsset()
    {
        if ($this->containerUtil->isFrontend()) {
            if ($this->encoreFrontendAsset) {
                $this->encoreFrontendAsset->addActiveEntrypoint('contao-tab-control-bundle');
            }

            $GLOBALS['TL_JAVASCRIPT']['huh_contao-tab-control-bundle_bootstrap-tabs'] = 'bundles/heimrichhannottabcontrol/bootstrap-tabs.js';
            $GLOBALS['TL_JAVASCRIPT']['huh_contao-tab-control-bundle']                = 'bundles/heimrichhannottabcontrol/contao-tab-control-bundle.js';
        } else {
            $GLOBALS['TL_CSS']['huh_contao-tab-control-bundle_backend'] = 'bundles/heimrichhannottabcontrol/contao-tab-control-bundle-backend.css';
        }
    }
}
