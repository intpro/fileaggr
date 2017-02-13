<?php

namespace Interpro\FileAggr\Executors;

use Interpro\Core\Contracts\Executor\BUpdateExecutor;
use Interpro\Core\Contracts\Ref\ARef;
use Interpro\Core\Contracts\Taxonomy\Fields\OwnField;
use Interpro\FileAggr\Contracts\Operation\SaveOperation;
use Interpro\FileAggr\Contracts\Settings\Collection\FileSettingsSet;

class UpdateExecutor implements BUpdateExecutor
{
    private $saveOperation;
    private $settingsSet;

    public function __construct(SaveOperation $saveOperation, FileSettingsSet $settingsSet)
    {
        $this->saveOperation = $saveOperation;
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
     * @param mixed $user_attrs
     *
     * @return void
     */
    public function update(ARef $ref, OwnField $own, $user_attrs)
    {
        if(!is_array($user_attrs))
        {
            $user_attrs = [];
        }

        $fileSetting = $this->settingsSet->getFile($own);

        $this->saveOperation->execute($ref, $fileSetting, $user_attrs);
    }
}
