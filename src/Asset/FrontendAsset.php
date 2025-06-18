<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\TabControlBundle\Asset;

use HeimrichHannot\EncoreContracts\PageAssetsTrait;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class FrontendAsset implements ServiceSubscriberInterface
{
    use PageAssetsTrait;

    /**
     * FrontendAsset constructor.
     */
    public function __construct(private readonly Utils $utils) {}

    public function addFrontendAsset(): void
    {
        if (!$this->utils->container()->isFrontend()) {
            return;
        }

        $this->addPageEntrypoint('contao-tab-control-bundle', [
            'TL_JAVASCRIPT' => [
                'huh_contao-tab-control-bundle' => 'bundles/heimrichhannottabcontrol/contao-tab-control-bundle.js',
            ],
            'TL_CSS' => [
                'huh_contao-tab-control-bundle' => 'bundles/heimrichhannottabcontrol/contao-tab-control-bundle.css',
            ],
        ]);
    }
}
