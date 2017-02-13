<?php

namespace Interpro\FileAggr\Operation\Owner;

use Interpro\Core\Contracts\Ref\ARef;
use Interpro\FileAggr\Contracts\Operation\SaveOperation as SaveOperationInterface;
use Interpro\FileAggr\Contracts\Operation\Owner\OwnerSaveOperationsCall as OwnerSaveOperationsCallInterface;

class OwnerSaveOperationsCall implements OwnerSaveOperationsCallInterface
{
    private $saveFile;

    public function __construct(SaveOperationInterface $saveFile)
    {
        $this->saveFile = $saveFile;
    }

    public function execute(ARef $aRef, array $user_attrs = [])
    {
        $ownerType = $aRef->getType();
        $fields = $ownerType->getOwns()->getTyped('file');

        foreach($fields as $fileField)
        {
            $file_name = $fileField->getName();

            if(array_key_exists($file_name, $user_attrs))//Только если есть что сохранять для этого файла
            {
                $this->saveFile->execute($aRef, $fileField, $user_attrs[$file_name]);
            }
        }
    }

}
