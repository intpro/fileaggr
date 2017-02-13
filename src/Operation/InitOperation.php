<?php

namespace Interpro\FileAggr\Operation;

use Interpro\Core\Contracts\Ref\ARef;
use Interpro\FileAggr\Contracts\Operation\InitOperation as InitOperationInterface;
use Interpro\FileAggr\Contracts\Settings\FileSetting;

class InitOperation extends Operation implements InitOperationInterface
{
    /**
     * @param \Interpro\Core\Contracts\Ref\ARef
     * @param \Interpro\FileAggr\Contracts\Settings\FileSetting $fileSetting
     * @param array $user_attrs
     *
     * @return void
     */
    public function execute(ARef $aRef, FileSetting $fileSetting, array $user_attrs = [])
    {
        $this->checkOwner($aRef);

        $user_attrs['link'] = '';

        $this->dbAgent->fileToDb($aRef, $fileSetting, $user_attrs, true);
    }

}
