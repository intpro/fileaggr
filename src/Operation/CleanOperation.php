<?php

namespace Interpro\FileAggr\Operation;

use Interpro\Core\Contracts\Ref\ARef;
use Interpro\FileAggr\Contracts\Operation\CleanOperation as CleanOperationInterface;
use Interpro\FileAggr\Contracts\Settings\FileSetting;

class CleanOperation extends Operation implements CleanOperationInterface
{
    /**
     * @param \Interpro\Core\Contracts\Ref\ARef
     * @param \Interpro\FileAggr\Contracts\Settings\FileSetting $fileSetting
     *
     * @return void
     */
    public function execute(ARef $aRef, FileSetting $fileSetting)
    {
        $this->checkOwner($aRef);

        $this->deleteAllFiles($aRef, $fileSetting);

        $this->dbAgent->fileToDb($aRef, $fileSetting, ['link' => '']);
    }
}
