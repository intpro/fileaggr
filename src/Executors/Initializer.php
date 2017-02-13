<?php

namespace Interpro\FileAggr\Executors;

use Interpro\Core\Contracts\Executor\BInitializer;
use Interpro\Core\Contracts\Ref\ARef;
use Interpro\Core\Contracts\Taxonomy\Fields\OwnField;
use Interpro\FileAggr\Contracts\Operation\InitOperation;
use Interpro\FileAggr\Contracts\Settings\Collection\FileSettingsSet;

class Initializer implements BInitializer
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
     * @param mixed $user_attrs
     *
     * @return void
     */
    public function init(ARef $ref, OwnField $own, $user_attrs = null)
    {
        if(!is_array($user_attrs))
        {
            $user_attrs = [];
        }

        $fileSetting = $this->settingsSet->getFile($own);

        $this->operation->execute($ref, $fileSetting, $user_attrs);
    }
}
