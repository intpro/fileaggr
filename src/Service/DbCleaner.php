<?php

namespace Interpro\FileAggr\Service;

use Interpro\Core\Contracts\Taxonomy\Taxonomy;
use Interpro\Core\Taxonomy\Enum\TypeRank;
use Interpro\FileAggr\Contracts\Settings\Collection\FileSettingsSet;
use Interpro\FileAggr\Model\File;
use Interpro\Service\Contracts\Cleaner as CleanerInterface;
use Interpro\Service\Enum\Artefact;

class DbCleaner implements CleanerInterface
{
    private $taxonomy;
    private $settingsSet;
    private $consoleOutput;

    public function __construct(Taxonomy $taxonomy, FileSettingsSet $settingsSet)
    {
        $this->taxonomy = $taxonomy;
        $this->settingsSet = $settingsSet;
        $this->consoleOutput = new \Symfony\Component\Console\Output\ConsoleOutput();
    }

    /**
     * @param callable $action
     *
     * @return bool
     */
    private function strategy(callable $action)
    {
        $report = false;

        $wehave = File::all();

        foreach($wehave as $model)
        {
            $entity_name = $model->entity_name;

            $name = $model->name;

            if(!$this->taxonomy->exist($entity_name))
            {
                $action(1, $model);
                $report = true;
            }
            else
            {
                $ownerType = $this->taxonomy->getType($entity_name);

                if($ownerType->getRank() === TypeRank::OWN)
                {
                    $action(2, $model);
                    $report = true;
                }
                elseif(!$ownerType->ownExist($name))
                {
                    $action(3, $model);
                    $report = true;
                }
                elseif($ownerType->getFieldType($name)->getName() !== 'file')
                {
                    $action(4, $model);
                    $report = true;
                }
            }
        }

        return $report;
    }

    /**
     * @return bool
     */
    public function inspect()
    {
        $action = function($flag, $model)
        {
            $entity_name = $model->entity_name;
            $entity_id   = $model->entity_id;

            $name = $model->name;

            if($flag === 1)
            {
                $message = 'FileAggr file('.$entity_id.'): обнаружена запись для типа хозяина'.$entity_name.' не найденого в таксономии.';
            }
            elseif($flag === 2)
            {
                $message = 'FileAggr file('.$entity_id.'): обнаружена запись для типа хозяина'.$entity_name.' не соответствующего ранга.';
            }
            elseif($flag === 3)
            {
                $message = 'FileAggr file('.$entity_id.'): обнаружена запись несуществующего поля '.$name.' для хозяина '.$entity_name.'.';
            }
            elseif($flag === 4)
            {
                $message = 'FileAggr file('.$entity_id.'): обнаружена запись несуществующего поля '.$name.' для хозяина '.$entity_name.'.';
            }
            else
            {
                return;
            }

            $this->consoleOutput->writeln($message);
        };

        $report = $this->strategy($action);

        return $report;
    }

    /**
     * @return void
     */
    public function clean()
    {
        $action = function($flag, $model)
        {
            $entity_name = $model->entity_name;
            $entity_id   = $model->entity_id;

            $name = $model->name;

            $model->delete();

            if($flag === 1)
            {
                $message = 'FileAggr file('.$entity_id.'): удалена запись для типа хозяина'.$entity_name.' не найденого в таксономии.';
            }
            elseif($flag === 2)
            {
                $message = 'FileAggr file('.$entity_id.'): удалена запись для типа хозяина'.$entity_name.' не соответствующего ранга.';
            }
            elseif($flag === 3)
            {
                $message = 'FileAggr file('.$entity_id.'): удалена запись несуществующего поля '.$name.' для хозяина '.$entity_name.'.';
            }
            elseif($flag === 4)
            {
                $message = 'FileAggr file('.$entity_id.'): удалена запись несуществующего поля '.$name.' для хозяина '.$entity_name.'.';
            }
            else
            {
                return;
            }

            $this->consoleOutput->writeln($message);
        };

        $this->strategy($action);
    }

    /**
     * @return string
     */
    public function getArtefact()
    {
        return Artefact::DB_ROW;
    }

    /**
     * @return string
     */
    public function getFamily()
    {
        return 'fileaggr';
    }
}
