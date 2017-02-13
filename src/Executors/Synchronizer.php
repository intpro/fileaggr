<?php

namespace Interpro\FileAggr\Executors;

use Interpro\Core\Contracts\Ref\ARef;

use Interpro\Core\Contracts\Executor\OwnSynchronizer as OwnSynchronizerInterface;
use Interpro\Core\Contracts\Taxonomy\Fields\OwnField;
use Interpro\Core\Taxonomy\Enum\TypeMode;
use Interpro\FileAggr\Contracts\Operation\InitOperation;
use Interpro\FileAggr\Contracts\Settings\Collection\FileSettingsSet;
use Interpro\FileAggr\Exception\FileAggrException;

class Synchronizer implements OwnSynchronizerInterface
{

    private $operation;
    private $settingsSet;

    public function __construct(InitOperation $operation, FileSettingsSet $settingsSet)
    {
        $this->operation = $operation;
        $this->settingsSet = $settingsSet;
    }

    /**
     * @return string
     */
    public function getFamily()
    {
        return 'fileaggr';
    }

    /**
     * @param \Interpro\Core\Contracts\Ref\ARef $ref
     * @param \Interpro\Core\Contracts\Taxonomy\Fields\OwnField $own
     *
     * @return void
     */
    public function sync(ARef $ref, OwnField $own)
    {
        $type = $own->getFieldType();
        $name = $type->getName();
        $mode = $type->getMode();

        if($name !== 'file' or $mode !== TypeMode::MODE_B)
        {
            throw new FileAggrException('Синхронизатор предназначен для поля типа file(B), передано: '.$name.'('.$mode.')!');
        }

        $fileSetting = $this->settingsSet->getFile($own);

        $this->operation->execute($ref, $fileSetting);

    }

}
