<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\TabControlBundle\Command;


use Contao\ContentModel;
use Contao\CoreBundle\Command\AbstractLockedCommand;
use Contao\Model;
use Contao\Model\Collection;
use HeimrichHannot\TabControlBundle\ContentElement\TabControlSeparatorElement;
use HeimrichHannot\TabControlBundle\ContentElement\TabControlStartElement;
use HeimrichHannot\TabControlBundle\ContentElement\TabControlStopElement;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigrationCommand extends AbstractLockedCommand
{
    /**
     * @var bool
     */
    protected $dryRun = false;
    /**
     * @var array
     */
    private $migrationSql;
    private $upgradeNotices;

    protected function configure()
    {
        $this
            ->setName('huh:tabcontrol:migrate')
            ->setDescription('Contao Tab Control Bundle migration.')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, "Performs a run without writing to database.")
        ;

    }


    /**
     * Executes the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    protected function executeLocked(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Contao Tab Control Bundle migration');
        if ($input->hasOption('dry-run') && $input->getOption('dry-run'))
        {
            $this->dryRun = true;
            $io->note("Dry run enabled, no data will be changed.");
            $io->newLine();
        }
        $this->getContainer()->get('contao.framework')->initialize();

        $io->text("Please select from where you want to upgrade. Select contao-teaser, if you upgrade from a bundle 0.x release to 1.x.");
        $question = new ChoiceQuestion(
            "Please select upgrade option:",
            [
                "fry_accessible_tabs",
                "<0.4",
                "cancel"
            ]
        );
        $migration = $io->askQuestion($question);

        switch ($migration)
        {
            case 'fry_accessible_tabs':
                $result = $this->migrateFromFryAccessibleTabs($io);
                break;
            case '<0.4':
                $result = $this->migrateFromLower0_4($io);
                break;
            case 'cancel':
                $this->finishedWithoutChanges($io);
                return 0;
            default:
                $io->error("Given migration ".$migration." is not valid.");
                return 1;
        }



        if ($this->hasMigrationSql())
        {
            $io->section("Migration SQL Command");
            $io->text("These are the MySql commands, if you want to do a database merge later while keeping newly added settings. Add the following lines to your database migration scripts.");
            $io->newLine();
            $io->text($this->getMigrationSql());
        }

        $io->success("Finished migration to tab control bundle.");

        return 0;


    }

    /**
     * Collect modules
     *
     * @param array $types
     * @return Collection|Model[]|Model|null
     */
    protected function collect(array $types): ?Collection
    {
        $options['column'] = [
            'tl_content.type IN (' . implode(',', array_map(function ($type) {
                return '"' . addslashes($type) . '"';
            }, $types)) . ')'
        ];
        return ContentModel::findAll($options);
    }

    public function migrateFromFryAccessibleTabs(SymfonyStyle $io)
    {
        $contentElementTypes = [
            'accessible_tabs_start',
            'accessible_tabs_separator',
            'accessible_tabs_stop',
        ];
        $contentElements = $this->collect($contentElementTypes);

        if (!$contentElements) {
            $io->text("Found no content element from fry accessibility tabs module.");
            return 0;
        }

        $io->text("Found <fg=yellow>".$contentElements->count()."</> elements.");

        foreach ($contentElements as $model)
        {
            $data = $this->getContainer()->get('huh.tab_control.helper.structure_tabs')->structureTabsByContentElement($model, '', [
                'startElement' => 'accessible_tabs_start',
                'seperatorElement' => 'accessible_tabs_separator',
                'stopElement' => 'accessible_tabs_stop',
            ]);

            if ($model->id === $data['elements'][1]['id'])
            {
                if ($model->type === 'accessible_tabs_separator')
                {
                    $this->addMigrationSql('DELETE FROM tl_content WHERE id='.$model->id.';');
                    if (!$this->dryRun)
                    {
                        $model->delete();
                    }
                    return 0;
                }
            }

            if ('accessible_tabs_start' === $model->type)
            {
                if ($data['elements'][0]['id'] !== $model->id)
                {
                    $io->error("Element ids not correct. Must be an error! Skipping");
                    return 1;
                }
                if ($data['elements'][1]['type'] !== 'accessible_tabs_separator')
                {
                    $io->error('Second element of accessiblity tab group must be an seperator element. That is not the case. Skipping.');
                }
                $model->type = TabControlStartElement::TYPE;
                $model->tabControlHeadline = $data['elements'][1]['accessible_tabs_title'];
                $model->tabControlRememberLastTab = $model->accessible_tabs_save_state;

                $this->addMigrationSql("UPDATE tl_content SET type='".$model->type."', tabControlHeadline='".$model->tabControlHeadline."' , tabControlRememberLastTab='".$model->tabControlRememberLastTab."' WHERE id=".$model->id.";");
            }

            if ('accessible_tabs_separator' === $model->type)
            {
                $model->type = TabControlSeparatorElement::TYPE;
                $model->tabControlHeadline = $model->accessible_tabs_title;
                $this->addMigrationSql("UPDATE tl_content SET type='".$model->type."', tabControlHeadline='".$model->tabControlHeadline."' WHERE id=".$model->id.";");
            }

            if ('accessible_tabs_stop' === $model->type)
            {
                $model->type = TabControlStopElement::TYPE;
                $this->addMigrationSql("UPDATE tl_content SET type='".$model->type."' WHERE id=".$model->id.";");
            }

            $this->saveModel($model);

            return 0;
        }
    }

    /**
     * Fixes content element typo in version until 0.3.
     *
     * @param SymfonyStyle $io
     * @return int
     */
    public function migrateFromLower0_4(SymfonyStyle $io)
    {
        $contentElementTypes = [
            'tabcontrolSeperator',
        ];
        $contentElements = $this->collect($contentElementTypes);

        if (!$contentElements) {
            $io->text("Found no content element from versions < 0.4 of tab control bundle.");
            return 0;
        }

        $io->text("Found <fg=yellow>".$contentElements->count()."</> elements.");

        $io->progressStart($contentElements->count());

        /** @var ContentModel $contentElement */
        foreach ($contentElements as $contentElement)
        {
            $contentElement->type = TabControlSeparatorElement::TYPE;
            $this->saveModel($contentElement);
            $io->progressAdvance();
        }
        $io->progressFinish();
        return 0;
    }

    protected function saveModel(ContentModel $contentModel)
    {
        if (!$this->dryRun)
        {
            $contentModel->tstamp = time();
            $contentModel->save();
        }
    }

    protected function finishedWithoutChanges(SymfonyStyle $io)
    {
        $io->success("Finished command without doing anything.");
    }

    protected function addMigrationSql(string $sql): void
    {
        $this->migrationSql[] = $sql;
    }

    protected function getMigrationSql(): array
    {
        return $this->migrationSql;
    }

    public function hasMigrationSql(): bool {
        return !empty($this->migrationSql);
    }
}