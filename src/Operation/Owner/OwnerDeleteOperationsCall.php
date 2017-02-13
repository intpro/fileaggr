<?php

namespace Interpro\FileAggr\Operation\Owner;

use Interpro\Core\Contracts\Ref\ARef;
use Interpro\FileAggr\Contracts\Operation\DeleteOperation as DeleteOperationInterface;
use Interpro\FileAggr\Contracts\Operation\Owner\OwnerDeleteOperationsCall as OwnerDeleteOperationsCallInterface;
use Interpro\FileAggr\Contracts\Settings\Collection\FileSettingsSet;

class OwnerDeleteOperationsCall implements OwnerDeleteOperationsCallInterface
{
    private $deleteFile;
    private $settingsSet;

    public function __construct(DeleteOperationInterface $deleteFile, FileSettingsSet $settingsSet)
    {
        $this->deleteFile = $deleteFile;
        $this->settingsSet = $settingsSet;
    }

    public function execute(ARef $aRef)
    {
        $ownerType = $aRef->getType();
        $fields = $ownerType->getOwns()->getTyped('file');

        foreach($fields as $fileField)
        {
            $fileSetting = $this->settingsSet->getFile($fileField);
            $this->deleteFile->execute($aRef, $fileSetting);
        }
    }

}
