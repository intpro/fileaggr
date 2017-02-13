<?php

namespace Interpro\FileAggr\Operation\Owner;

use Interpro\Core\Contracts\Ref\ARef;
use Interpro\FileAggr\Contracts\Operation\InitOperation as InitOperationInterface;
use Interpro\FileAggr\Contracts\Operation\Owner\OwnerInitOperationsCall as OwnerInitOperationsCallInterface;

class OwnerInitOperationsCall implements OwnerInitOperationsCallInterface
{
    private $initFile;

    public function __construct(InitOperationInterface $initFile)
    {
        $this->initFile = $initFile;
    }

    public function execute(ARef $aRef, array $user_attrs = [])
    {
        $ownerType = $aRef->getType();
        $fields = $ownerType->getOwns()->getTyped('file');

        foreach($fields as $fileField)
        {
            $file_name = $fileField->getName();

            if(array_key_exists($file_name, $user_attrs))
            {
                $file_attrs = $user_attrs[$file_name];
            }
            else
            {
                $file_attrs = [];
            }

            $this->initFile->execute($aRef, $fileField, $file_attrs);
        }
    }

}
