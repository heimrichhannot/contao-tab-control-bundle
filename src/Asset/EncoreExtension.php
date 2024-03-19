<?php

namespace HeimrichHannot\TabControlBundle\Asset;

use HeimrichHannot\EncoreContracts\EncoreEntry;
use HeimrichHannot\EncoreContracts\EncoreExtensionInterface;
use HeimrichHannot\TabControlBundle\HeimrichHannotTabControlBundle;

class EncoreExtension implements EncoreExtensionInterface
{

    /**
     * @inheritDoc
     */
    public function getBundle(): string
    {
        return HeimrichHannotTabControlBundle::class;
    }

    /**
     * @inheritDoc
     */
    public function getEntries(): array
    {
        return [
            EncoreEntry::create('contao-tab-control-bundle', 'src/Resources/assets/js/contao-tab-control-bundle.js')
                ->addJsEntryToRemoveFromGlobals('huh_contao-tab-control-bundle_bootstrap-tabs')
                ->addJsEntryToRemoveFromGlobals('huh_contao-tab-control-bundle'),
        ];
    }
}