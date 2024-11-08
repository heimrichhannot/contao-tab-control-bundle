<?php

namespace HeimrichHannot\TabControlBundle\Migration;

use Contao\CoreBundle\Migration\MigrationInterface;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use HeimrichHannot\TabControlBundle\Controller\ContentElement\TabControlSeparatorElementController;

class Version0400Migration implements MigrationInterface
{
    public function __construct(
        private readonly Connection $connection,
    )
    {
    }

    public function getName(): string
    {
        return 'Tab Control 0.4.0 migration';
    }

    /**
     * @throws Exception
     */
    public function shouldRun(): bool
    {
        $result = $this->connection->executeQuery(
            'SELECT COUNT(id) FROM tl_content WHERE type = ?',
            ['tabcontrolSeperator']
        );

        return $result->fetchOne() > 0;
    }

    /**
     * @throws Exception
     */
    public function run(): MigrationResult
    {
        $result = $this->connection->executeQuery(
            'SELECT id FROM tl_content WHERE type = ?',
            ['tabcontrolSeperator']
        );

        $rowCount = $result->rowCount();
        if ($rowCount < 1) {
            return new MigrationResult(true, $this->getName().' finished without any changes.');
        }

        while ($contentElement = $result->fetchAssociative()) {
            $this->connection->executeQuery(
                'UPDATE tl_content SET type = ?, tstamp = ? WHERE id = ?',
                [TabControlSeparatorElementController::TYPE, time(), $contentElement['id']]
            );
        }

        return new MigrationResult(true, $this->getName().' finished successfully. Migrated '. $rowCount.' elements.');
    }
}