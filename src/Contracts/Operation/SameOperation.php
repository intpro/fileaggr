<?php

namespace Interpro\FileAggr\Contracts\Operation;

use Interpro\Core\Contracts\Ref\ARef;
use Interpro\FileAggr\Contracts\Settings\FileSetting;

interface SameOperation
{
    /**
     * @param \Interpro\Core\Contracts\Ref\ARef
     * @param \Interpro\FileAggr\Contracts\Settings\FileSetting $fileSetting
     */
    public function execute(ARef $aRef, FileSetting $fileSetting);
}
