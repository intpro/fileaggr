<?php

namespace Interpro\FileAggr\Operation;

use Interpro\Core\Contracts\Ref\ARef;
use Interpro\FileAggr\Contracts\Operation\DeleteOperation as DeleteOperationInterface;
use Interpro\FileAggr\Contracts\Settings\FileSetting;

class DeleteOperation extends Operation implements DeleteOperationInterface
{
    /**
     * @param \Interpro\Core\Contracts\Ref\ARef $aRef
     * @param \Interpro\FileAggr\Contracts\Settings\FileSetting $fileSetting
     *
     * @return void
     */
    public function execute(ARef $aRef, FileSetting $fileSetting)
    {
        $this->checkOwner($aRef);

        $this->deleteAllFiles($aRef, $fileSetting);

        $this->dbAgent->deleteFile($aRef, $fileSetting);
    }

}
