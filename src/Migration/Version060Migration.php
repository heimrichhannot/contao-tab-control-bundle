<?php

namespace HeimrichHannot\TabControlBundle\Migration;

use Contao\ContentModel;
use Contao\CoreBundle\Migration\MigrationInterface;
use Contao\CoreBundle\Migration\MigrationResult;
use Contao\Database;

class Version060Migration implements MigrationInterface
{

    public function getName(): string
    {
        return 'Tab Control 0.6.0 migration';
    }

    public function shouldRun(): bool
    {
        if (!Database::getInstance()->fieldExists('tabControlLink', 'tl_content')) {
            return false;
        }

        if (ContentModel::findBy(['tabControlLink IS NULL'], null)) {
            return true;
        }

        return false;
    }

    public function run(): MigrationResult
    {
        $elements = ContentModel::findBy(['tabControlLink IS NULL'], null);
        if ($elements) {
            while ($elements->next()) {
                $elements->tabControlLink = '';
                $elements->save();
            }
        }

        return new MigrationResult(true, 'Migrated tab control tabControlLink field.');
    }
}