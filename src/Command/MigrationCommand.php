<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\TabControlBundle\Command;

use Contao\ContentModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Model;
use Contao\Model\Collection;
use Contao\StringUtil;
use HeimrichHannot\TabControlBundle\Controller\ContentElement\TabControlSeparatorElementController;
use HeimrichHannot\TabControlBundle\Controller\ContentElement\TabControlStartElementController;
use HeimrichHannot\TabControlBundle\Controller\ContentElement\TabControlStopElementController;
use HeimrichHannot\TabControlBundle\Helper\StructureTabHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigrationCommand extends Command
{
    protected const MIGRATION_FRY = 'fry_accessible_tabs';
    protected const MIGRATION_BOOTSTRAPPER = 'bootstrapper-tabs';
    protected const MIGRATIONS = [
        self::MIGRATION_FRY,
        self::MIGRATION_BOOTSTRAPPER,
    ];

    protected bool $dryRun = false;
    protected SymfonyStyle $io;

    private array $migrationSql = [];
    private readonly ContaoFramework $contaoFramework;

    public function __construct(private readonly StructureTabHelper $structureTabHelper, ContaoFramework $contaoFramework)
    {
        parent::__construct();
        $this->contaoFramework = $contaoFramework;
    }

    public function migrateFromBootstrapperTabs(SymfonyStyle $io): int
    {
        $contentElementTypes = [
            'tabcontrol',
        ];
        $contentElements = $this->collect($contentElementTypes);

        if (!$contentElements) {
            $io->text('Found no content element from bootstrapper tabs module.');

            return 0;
        }

        $io->text('Found <fg=yellow>'.$contentElements->count().'</> elements.');

        $tabgroupCache = [];
        $depth = 0;
        $depthNext = 0;

        $contentElements = $contentElements->getModels();

        if (!$io->isVerbose()) {
            $io->progressStart(\count($contentElements));
        } else {
            $io->newLine();
        }

        foreach ($contentElements as $index => $model) {
            if (!$io->isVerbose()) {
                $io->progressAdvance();
            }

            if ('tabcontroltab' === $model->tabType) {
                if ($depthNext > $depth) {
                    $depth = $depthNext;
                }
                $tabgroupCache[$depth] = [];
                $tabgroupCache[$depth]['structure'] = [$model];
                $tabgroupCache[$depth]['headlines'] = array_column(StringUtil::deserialize($model->tab_tabs, true), 'tab_tabs_name');
                $model->tabControlHeadline = $tabgroupCache[$depth]['headlines'][0];
                $model->tabControlRememberLastTab = $model->tab_remember;
                $model->type = TabControlStartElementController::TYPE;
                $this->saveModel($model, ['tabControlHeadline', 'tabControlRememberLastTab', 'type']);
                ++$depthNext;

                continue;
            }

            if ('tabcontrolstart' === $model->tabType) {
                if ('tabcontroltab' === $contentElements[($index - 1)]->tabType) {
                    if ($io->isVerbose()) {
                        $io->text('Delete content element id '.$model->id.' and type'.$model->type.'.');
                    }
                    $this->deleteModel($model);
                } else {
                    $tabgroupCache[$depth]['structure'][] = $model;
                    $model->tabControlHeadline = $tabgroupCache[$depth]['headlines'][\count($tabgroupCache[$depth]['structure']) - 1];
                    $model->type = TabControlSeparatorElementController::TYPE;

                    if ($io->isVerbose()) {
                        $io->text('Migrate content element id '.$model->id.' and type'.$model->type.' to '.TabControlStopElementController::TYPE.'.');
                    }
                    $this->saveModel($model, ['type', 'tabControlHeadline']);
                }

                if ('tabcontrolstop' === $contentElements[($index + 1)]->tabType) {
                    if ($io->isVerbose()) {
                        $io->text('Delete content element id '.$contentElements[($index + 1)]->id.' and type'.$contentElements[($index + 1)]->type.'.');
                    }
                    $this->deleteModel($contentElements[($index + 1)]);
                }

                continue;
            }

            if ('tabcontrol_end' === $model->tabType) {
                if ($io->isVerbose()) {
                    $io->text('Migrate content element id '.$model->id.' and type'.$model->type.' to '.TabControlStopElementController::TYPE.'.');
                }
                $tabgroupCache[$depth]['structure'] = [$index => $model];
                $model->type = TabControlStopElementController::TYPE;
                $this->saveModel($model, ['type']);
                --$depthNext;

                if ($depth > 0) {
                    --$depth;
                }
            }
        }

        if (!$io->isVerbose()) {
            $io->progressFinish();
        }

        return 0;
    }

    public function migrateFromFryAccessibleTabs(SymfonyStyle $io): int
    {
        $contentElementTypes = [
            'accessible_tabs_start',
            'accessible_tabs_separator',
            'accessible_tabs_stop',
        ];
        $contentElements = $this->collect($contentElementTypes);

        if (!$contentElements) {
            $io->text('Found no content element from fry accessibility tabs module.');

            return 0;
        }

        $io->text('Found <fg=yellow>'.$contentElements->count().'</> elements.');

        foreach ($contentElements as $model) {
            $data = $this->structureTabHelper->structureTabsByContentElement($model, '', [
                'startElement' => 'accessible_tabs_start',
                'separatorElement' => 'accessible_tabs_separator',
                'stopElement' => 'accessible_tabs_stop',
            ]);

            if ($model->id === $data['elements'][1]['id']) {
                if ('accessible_tabs_separator' === $model->type) {
                    $this->addMigrationSql('DELETE FROM tl_content WHERE id='.$model->id.';');

                    if (!$this->dryRun) {
                        $model->delete();
                    }

                    return 0;
                }
            }

            if ('accessible_tabs_start' === $model->type) {
                if ($data['elements'][0]['id'] !== $model->id) {
                    $io->error('Element ids not correct. Must be an error! Skipping');

                    return 1;
                }

                if ('accessible_tabs_separator' !== $data['elements'][1]['type']) {
                    $io->error('Second element of accessiblity tab group must be an separator element. That is not the case. Skipping.');
                }
                $model->type = TabControlStartElementController::TYPE;
                $model->tabControlHeadline = $data['elements'][1]['accessible_tabs_title'];
                $model->tabControlRememberLastTab = $model->accessible_tabs_save_state;

                $this->addMigrationSql("UPDATE tl_content SET type='".$model->type."', tabControlHeadline='".$model->tabControlHeadline."' , tabControlRememberLastTab='".$model->tabControlRememberLastTab."' WHERE id=".$model->id.';');
            }

            if ('accessible_tabs_separator' === $model->type) {
                $model->type = TabControlSeparatorElementController::TYPE;
                $model->tabControlHeadline = $model->accessible_tabs_title;
                $this->addMigrationSql("UPDATE tl_content SET type='".$model->type."', tabControlHeadline='".$model->tabControlHeadline."' WHERE id=".$model->id.';');
            }

            if ('accessible_tabs_stop' === $model->type) {
                $model->type = TabControlStopElementController::TYPE;
                $this->addMigrationSql("UPDATE tl_content SET type='".$model->type."' WHERE id=".$model->id.';');
            }

            $this->saveModel($model);


        }

        return 0;
    }

    public function hasMigrationSql(): bool
    {
        return !empty($this->migrationSql);
    }

    protected function configure()
    {
        $this
            ->setName('huh:tabcontrol:migrate')
            ->setDescription('Contao Tab Control Bundle migration.')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Performs a run without writing to database.')
            ->addOption('migration', null, InputOption::VALUE_REQUIRED, 'Do migration directly without interrupt. Options: '.implode(', ', static::MIGRATIONS))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Contao Tab Control Bundle migration');

        if ($input->hasOption('dry-run') && $input->getOption('dry-run')) {
            $this->dryRun = true;
            $io->note('Dry run enabled, no data will be changed.');
            $io->newLine();
        }
        $this->contaoFramework->initialize();
        $this->io = $io;

        if ($input->hasOption('migration') && !empty($input->getOption('migration'))) {
            $migration = $input->getOption('migration');
        } else {
            $io->text('Please select from where you want to upgrade. Select contao-teaser, if you upgrade from a bundle 0.x release to 1.x.');
            $question = new ChoiceQuestion(
                'Please select upgrade option:',
                array_merge(static::MIGRATIONS, ['cancel'])
            );
            $migration = $io->askQuestion($question);
        }

        switch ($migration) {
            case static::MIGRATION_FRY:
                $result = $this->migrateFromFryAccessibleTabs($io);

                break;

            case static::MIGRATION_BOOTSTRAPPER:
                $result = $this->migrateFromBootstrapperTabs($io);

                break;

            case 'cancel':
                $this->finishedWithoutChanges($io);

                return 0;

            default:
                $io->error('Given migration '.$migration.' is not valid.');

                return 1;
        }

        if ($this->hasMigrationSql()) {
            $io->section('Migration SQL Command');
            $io->text('These are the MySql commands, if you want to do a database merge later while keeping newly added settings. Add the following lines to your database migration scripts.');
            $io->newLine();
            $io->text($this->getMigrationSql());
        }

        $io->success('Finished migration to tab control bundle.');

        return 0;
    }

    /**
     * Collect modules.
     *
     * @return Collection|Model[]|Model|null
     */
    protected function collect(array $types): ?Collection
    {
        $options['column'] = [
            'tl_content.type IN ('.implode(',', array_map(fn($type) => '"'.addslashes((string) $type).'"', $types)).')',
        ];

        return ContentModel::findAll($options);
    }

    protected function saveModel(ContentModel $contentModel, array $modifiedFields = [])
    {
        if (!$this->dryRun) {
            $contentModel->tstamp = time();
            $contentModel->save();
        }

        if (!empty($modifiedFields)) {
            $migrationSQL = 'UPDATE tl_content SET ';
            $set = '';

            foreach ($modifiedFields as $field) {
                if (is_numeric($contentModel->{$field})) {
                    $set .= $field.'='.$contentModel->{$field}.', ';
                } else {
                    $set .= $field.'="'.$contentModel->{$field}.'", ';
                }
            }
            $migrationSQL .= trim($set, ', ');
            $migrationSQL .= ' WHERE id='.$contentModel->id.';';

            $this->addMigrationSql($migrationSQL);
        }
    }

    protected function deleteModel(ContentModel $contentModel): void
    {
        $this->addMigrationSql('DELETE FROM tl_content WHERE id='.$contentModel->id.';');

        if (!$this->dryRun) {
            $contentModel->delete();
        }
    }

    protected function finishedWithoutChanges(SymfonyStyle $io): void
    {
        $io->success('Finished command without doing anything.');
    }

    protected function addMigrationSql(string $sql): void
    {
        $this->migrationSql[] = $sql;
    }

    protected function getMigrationSql(): array
    {
        return $this->migrationSql;
    }
}
